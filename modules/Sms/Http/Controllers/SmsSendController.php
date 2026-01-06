<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Sms\Http\Requests\SmsSendRequest;
use Modules\Sms\Models\SmsGateway;
use Modules\Sms\Models\SmsTemplate;
use Modules\Sms\Services\SmsGatewayService;
use Modules\Support\Http\Controllers\BackendController;

class SmsSendController extends BackendController
{
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

        $gateways = SmsGateway::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'is_default']);

        return inertia('Sms/Send/SmsSendForm', [
            'templates' => $templates,
            'gateways' => $gateways,
        ]);
    }

    public function store(SmsSendRequest $request, SmsGatewayService $smsGateway): RedirectResponse
    {
        $validated = $request->validated();

        $message = (string) ($validated['message'] ?? '');
        if ($message === '' && ! empty($validated['template_id'])) {
            $message = (string) SmsTemplate::query()
                ->where('is_active', true)
                ->whereKey($validated['template_id'])
                ->value('content');
        }

        if (trim($message) === '') {
            return back()->with('error', 'Message content is required.');
        }

        $phone = $this->normalizePhone($validated['phone']);
        if (strlen(preg_replace('/\D+/', '', $phone)) < 8) {
            return back()->with('error', 'Invalid phone number.');
        }

        try {
            $gatewayId = ! empty($validated['gateway_id']) ? (int) $validated['gateway_id'] : null;
            $smsGateway->send($phone, $message, $gatewayId);
        } catch (\Throwable $e) {
            Log::warning('Quick SMS send failed', ['error' => $e->getMessage()]);
            $msg = trim((string) $e->getMessage());

            return back()->with('error', $msg !== '' ? $msg : 'Failed to send SMS.');
        }

        return back()->with('success', 'SMS sent successfully.');
    }

    private function normalizePhone(string $phone): string
    {
        $trimmed = trim($phone);

        if (Str::startsWith($trimmed, '+')) {
            return '+'.preg_replace('/\D+/', '', substr($trimmed, 1));
        }

        return preg_replace('/\D+/', '', $trimmed);
    }
}
