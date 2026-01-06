<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sms\Http\Requests\SmsCampaignRequest;
use Modules\Sms\Jobs\ProcessSmsCampaignJob;
use Modules\Sms\Models\SmsCampaign;
use Modules\Sms\Models\SmsCampaignRecipient;
use Modules\Sms\Models\SmsTemplate;
use Modules\Support\Http\Controllers\BackendController;

class SmsCampaignController extends BackendController
{
    public function index(): Response
    {
        $searchTerm = request('searchTerm');
        $status = request('status');

        $campaigns = SmsCampaign::query()
            ->when($searchTerm, fn ($q) => $q->where('name', 'like', "%{$searchTerm}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with('template:id,name')
            ->latest('id')
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn (SmsCampaign $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'template_name' => $c->template?->name,
                'message_preview' => Str::limit($c->message, 60),
                'recipient_count' => $c->recipient_count,
                'sent_count' => $c->sent_count,
                'failed_count' => $c->failed_count,
                'status' => $c->status,
                'scheduled_at' => $c->scheduled_at?->format('M d, Y H:i'),
                'created_at' => $c->created_at?->format('M d, Y H:i'),
            ]);

        $statusOptions = [
            ['value' => '', 'label' => 'All Statuses'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'running', 'label' => 'Running'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'failed', 'label' => 'Failed'],
        ];

        return inertia('Sms/Campaign/SmsCampaignIndex', [
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    public function create(): Response
    {
        $templates = SmsTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'content'])
            ->map(fn (SmsTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'content' => $t->content,
            ]);

        return inertia('Sms/Campaign/SmsCampaignForm', [
            'templates' => $templates,
        ]);
    }

    public function store(SmsCampaignRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $message = (string) ($validated['message'] ?? '');
        if ($message === '' && ! empty($validated['template_id'])) {
            $message = (string) SmsTemplate::query()
                ->whereKey($validated['template_id'])
                ->value('content');
        }

        if (trim($message) === '') {
            return back()->with('error', 'Message content is required.');
        }

        $phones = $this->collectPhones($request, $validated);

        if (count($phones) === 0) {
            return back()->with('error', 'At least one valid phone number is required.');
        }

        try {
            $campaign = DB::transaction(function () use ($validated, $message, $phones) {
                $campaign = SmsCampaign::create([
                    'name' => $validated['name'],
                    'template_id' => $validated['template_id'] ?? null,
                    'message' => $message,
                    'recipient_count' => count($phones),
                    'status' => 'pending',
                    'scheduled_at' => $validated['scheduled_at'] ?? null,
                    'created_by' => auth()->id(),
                ]);

                $recipientData = [];
                foreach ($phones as $phone) {
                    $recipientData[] = [
                        'campaign_id' => $campaign->id,
                        'phone' => $phone,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach (array_chunk($recipientData, 500) as $chunk) {
                    SmsCampaignRecipient::insert($chunk);
                }

                return $campaign;
            });

            ProcessSmsCampaignJob::dispatch($campaign);

            return redirect()->route('sms.campaigns.index')
                ->with('success', "Campaign created with {$campaign->recipient_count} recipients. Processing started.");
        } catch (\Throwable $e) {
            Log::error('SMS Campaign creation failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Failed to create campaign. Please try again.');
        }
    }

    public function show(SmsCampaign $smsCampaign): Response
    {
        $recipients = $smsCampaign->recipients()
            ->latest('id')
            ->paginate(request('rowsPerPage', 20))
            ->withQueryString()
            ->through(fn (SmsCampaignRecipient $r) => [
                'id' => $r->id,
                'phone' => $r->phone,
                'status' => $r->status,
                'error_message' => $r->error_message,
                'sent_at' => $r->sent_at?->format('M d, Y H:i'),
            ]);

        return inertia('Sms/Campaign/SmsCampaignShow', [
            'campaign' => [
                'id' => $smsCampaign->id,
                'name' => $smsCampaign->name,
                'message' => $smsCampaign->message,
                'recipient_count' => $smsCampaign->recipient_count,
                'sent_count' => $smsCampaign->sent_count,
                'failed_count' => $smsCampaign->failed_count,
                'status' => $smsCampaign->status,
                'started_at' => $smsCampaign->started_at?->format('M d, Y H:i'),
                'completed_at' => $smsCampaign->completed_at?->format('M d, Y H:i'),
                'created_at' => $smsCampaign->created_at?->format('M d, Y H:i'),
            ],
            'recipients' => $recipients,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $trimmed = trim($phone);

        if (Str::startsWith($trimmed, '+')) {
            return '+'.preg_replace('/\D+/', '', substr($trimmed, 1));
        }

        return preg_replace('/\D+/', '', $trimmed);
    }

    /**
     * @param  array<string,mixed>  $validated
     * @return array<int,string>
     */
    private function collectPhones($request, array $validated): array
    {
        $raw = [];

        if (! empty($validated['to_phones'])) {
            $raw = array_merge($raw, $this->splitPhones((string) $validated['to_phones']));
        }

        if ($request->hasFile('numbers_file')) {
            /** @var UploadedFile|null $file */
            $file = $request->file('numbers_file');
            if ($file) {
                $raw = array_merge($raw, $this->phonesFromFile($file));
            }
        }

        $normalized = [];
        foreach ($raw as $value) {
            $phone = $this->normalizePhone((string) $value);
            if ($phone === '' || strlen(preg_replace('/\D+/', '', $phone)) < 8) {
                continue;
            }
            $normalized[$phone] = true;
        }

        return array_values(array_keys($normalized));
    }

    /**
     * @return array<int,string>
     */
    private function splitPhones(string $input): array
    {
        $input = str_replace(["\r\n", "\r"], "\n", $input);

        $parts = preg_split('/[\s,;]+/', $input) ?: [];
        $phones = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '') {
                $phones[] = $p;
            }
        }

        return $phones;
    }

    /**
     * @return array<int,string>
     */
    private function phonesFromFile(UploadedFile $file): array
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if (in_array($ext, ['csv', 'txt'], true)) {
            $content = (string) $file->get();
            $lines = preg_split('/\R+/', $content) ?: [];
            $phones = [];
            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '') {
                    continue;
                }

                $cells = str_getcsv($line);
                if (isset($cells[0]) && trim((string) $cells[0]) !== '') {
                    $phones[] = (string) $cells[0];
                }
            }

            return $phones;
        }

        try {
            $sheets = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray
            {
                /**
                 * @param  array<int, mixed>  $array
                 * @return array<int, mixed>
                 */
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);

            $phones = [];
            foreach ($sheets as $sheet) {
                foreach ($sheet as $row) {
                    if (isset($row[0]) && trim((string) $row[0]) !== '') {
                        $phones[] = (string) $row[0];
                    }
                }
            }

            return $phones;
        } catch (\Throwable $e) {
            Log::warning('Failed to parse Excel file for SMS', ['error' => $e->getMessage()]);

            return [];
        }
    }
}
