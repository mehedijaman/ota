<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Sms\Models\SmsSetting;
use Modules\Sms\Services\SmsGateway;
use Modules\Support\Http\Controllers\BackendController;

class SuperadminSmsSettingsController extends BackendController
{
    public function edit(): Response
    {
        $providersConfig = config('sms.providers', []);

        $providerOptions = collect(array_keys($providersConfig))
            ->map(fn (string $providerClass) => [
                'value' => $providerClass,
                'label' => class_basename($providerClass),
            ])
            ->values();

        $providerFields = collect($providersConfig)
            ->map(fn (array $credentials, string $providerClass) => array_keys($credentials))
            ->toArray();

        $settings = SmsSetting::query()
            ->orderByDesc('is_default')
            ->get(['provider_class', 'credentials', 'is_default', 'is_active'])
            ->keyBy('provider_class')
            ->map(fn (SmsSetting $row) => [
                'credentials' => $row->credentials ?? [],
                'is_default' => (bool) $row->is_default,
                'is_active' => (bool) $row->is_active,
            ]);

        $defaultProvider = optional(
            SmsSetting::query()->where('is_default', true)->first(['provider_class'])
        )->provider_class;

        return inertia('Sms/Superadmin/SmsSettings', [
            'providerOptions' => $providerOptions,
            'providerFields' => $providerFields,
            'settings' => $settings,
            'defaultProvider' => $defaultProvider,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $providersConfig = config('sms.providers', []);

        $validated = $request->validate([
            'provider_class' => ['required', 'string', 'in:'.implode(',', array_keys($providersConfig))],
            'credentials' => ['nullable', 'array'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $providerClass = $validated['provider_class'];
        $credentials = $validated['credentials'] ?? [];
        $isDefault = (bool) ($validated['is_default'] ?? false);
        $isActive = (bool) ($validated['is_active'] ?? true);

        DB::transaction(function () use ($providerClass, $credentials, $isDefault, $isActive) {
            if ($isDefault) {
                SmsSetting::query()->update(['is_default' => false]);
            }

            SmsSetting::query()->updateOrCreate(
                ['provider_class' => $providerClass],
                [
                    'credentials' => $credentials,
                    'is_default' => $isDefault,
                    'is_active' => $isActive,
                ]
            );
        });

        return redirect()->route('superadmin.sms.settings.edit')
            ->with('success', 'SMS settings updated.');
    }

    public function test(Request $request, SmsGateway $smsGateway): RedirectResponse
    {
        $providersConfig = config('sms.providers', []);

        $validated = $request->validate([
            'provider_class' => ['required', 'string', 'in:'.implode(',', array_keys($providersConfig))],
            'credentials' => ['nullable', 'array'],
            'test_phone' => ['required', 'string', 'max:32'],
            'test_message' => ['nullable', 'string', 'max:500'],
        ]);

        $phone = $this->normalizePhone($validated['test_phone']);
        $message = trim((string) ($validated['test_message'] ?? ''));
        if ($message === '') {
            $message = 'Test SMS from SlimPOS.';
        }

        try {
            $smsGateway->sendWith(
                $validated['provider_class'],
                (array) ($validated['credentials'] ?? []),
                $phone,
                $message
            );
        } catch (\Throwable $e) {
            $msg = trim((string) $e->getMessage());
            if ($msg === '') {
                $msg = 'Failed to send test SMS. Check credentials/provider.';
            } else {
                $msg = 'Failed to send test SMS: '.$msg;
            }

            return back()->with('error', $msg);
        }

        return back()->with('success', 'Test SMS sent.');
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
