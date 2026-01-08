<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;
use Modules\Setting\Http\Requests\SettingValidate;
use Modules\Setting\Services\SettingService;
use Modules\Support\Http\Controllers\BackendController;

class SettingController extends BackendController
{
    public function __construct(protected SettingService $settingService) {}

    public function index(): Response
    {
        return inertia('Setting/SettingIndex', [
            'settings' => $this->settingService->getAllForAdmin(),
        ]);
    }

    public function update(SettingValidate $request): RedirectResponse
    {
        $this->settingService->updateFromRequest($request->validated(), $request);

        return redirect()->route('setting.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function testEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $smtp = $this->settingService->getSmtpConfig();

        if (empty($smtp['host'])) {
            return redirect()->back()
                ->with('error', 'Please configure SMTP settings first.');
        }

        try {
            config([
                'mail.mailers.smtp.host' => $smtp['host'],
                'mail.mailers.smtp.port' => $smtp['port'] ?? 587,
                'mail.mailers.smtp.username' => $smtp['username'] ?? null,
                'mail.mailers.smtp.password' => $smtp['password'] ?? null,
                'mail.mailers.smtp.encryption' => $smtp['encryption'] ?? 'tls',
                'mail.from.address' => $smtp['from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $smtp['from_name'] ?? config('mail.from.name'),
            ]);

            Mail::raw('This is a test email from your application.', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Test Email - SMTP Configuration');
            });

            return redirect()->back()
                ->with('success', 'Test email sent successfully to '.$request->email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send test email: '.$e->getMessage());
        }
    }

    public function maintenance(): Response
    {
        return inertia('Setting/AppMaintenance');
    }
}
