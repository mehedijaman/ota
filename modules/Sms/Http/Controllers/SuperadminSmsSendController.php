<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Sms\Models\SmsTemplate;
use Modules\Sms\Services\SmsGateway;
use Modules\Support\Http\Controllers\BackendController;

class SuperadminSmsSendController extends BackendController
{
    public function create(): Response
    {
        $templates = SmsTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (SmsTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'content' => $t->content,
            ]);

        return inertia('Sms/Superadmin/SendPromotional', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request, SmsGateway $smsGateway): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => [
                'nullable',
                Rule::exists('sms_templates', 'id')->where('is_active', true),
            ],
            'message' => 'nullable|string|max:1000|required_without:template_id',

            'to_phone' => 'nullable|string|max:30|required_without_all:to_phones,numbers_file',
            'to_phones' => 'nullable|string|max:5000|required_without_all:to_phone,numbers_file',
            'numbers_file' => 'nullable|file|mimes:csv,txt,xlsx,xls|required_without_all:to_phone,to_phones',
        ]);

        $message = (string) ($validated['message'] ?? '');
        if ($message === '' && ! empty($validated['template_id'])) {
            $message = (string) SmsTemplate::query()
                ->where('is_active', true)
                ->whereKey($validated['template_id'])
                ->value('content');
        }

        if (trim($message) === '') {
            return back()->with('error', 'Message is required.');
        }

        $phones = $this->collectPhones($request, $validated);
        if (count($phones) === 0) {
            return back()->with('error', 'At least one valid phone number is required.');
        }

        $sent = 0;
        $failed = 0;
        $firstFailure = null;

        try {
            foreach ($phones as $phone) {
                try {
                    $smsGateway->send($phone, $message);
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    if ($firstFailure === null) {
                        $firstFailure = trim((string) $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Superadmin SMS send failed unexpectedly', [
                'error' => $e->getMessage(),
            ]);

            $msg = trim((string) $e->getMessage());

            return back()->with('error', $msg !== '' ? $msg : 'SMS failed.');
        }

        if ($sent > 0 && $failed === 0) {
            return back()->with('success', 'SMS sent.');
        }

        $summary = "Sent: {$sent}, Failed: {$failed}.";
        if ($firstFailure) {
            $summary .= " First failure: {$firstFailure}";
        }

        return back()->with($sent > 0 ? 'success' : 'error', $summary);
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
    private function collectPhones(Request $request, array $validated): array
    {
        $raw = [];

        if (! empty($validated['to_phone'])) {
            $raw[] = (string) $validated['to_phone'];
        }

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
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);
        } catch (\Throwable $e) {
            return [];
        }

        $phones = [];
        foreach ($sheets as $sheet) {
            foreach ($sheet as $row) {
                if (! is_array($row) || ! array_key_exists(0, $row)) {
                    continue;
                }

                $value = trim((string) $row[0]);
                if ($value !== '') {
                    $phones[] = $value;
                }
            }
        }

        return $phones;
    }
}
