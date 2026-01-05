<?php

namespace Modules\Sms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Sms\Models\SmsOtp;
use Modules\Sms\Services\SmsGateway;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class RegistrationOtpController
{
    public function send(Request $request, SmsGateway $smsGateway): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        if (! $this->looksLikePhone($phone)) {
            return response()->json(
                ['message' => 'Invalid phone number.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Basic anti-spam: one OTP per phone per 60 seconds
        $last = SmsOtp::query()
            ->where('purpose', 'register')
            ->where('phone', $phone)
            ->latest('id')
            ->first();

        if ($last && $last->created_at && $last->created_at->gt(now()->subSeconds(60))) {
            $availableAt = $last->created_at->copy()->addSeconds(60);
            $retryAfter = max(1, $availableAt->diffInSeconds(now()));

            return response()->json(
                [
                    'message' => 'Please wait before requesting another code.',
                    'retry_after' => $retryAfter,
                ],
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        $code = (string) random_int(100000, 999999);

        SmsOtp::create([
            'purpose' => 'register',
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        // Shiram/BTRC compliance: messages must be Bangla Unicode.
        $message = "SlimPOS যাচাইকরণ কোড: {$code}. ৫ মিনিটের মধ্যে ব্যবহার করুন।";

        $isTestMode = (bool) config('sms.otp.test_mode', false);

        if ($isTestMode) {
            return response()->json([
                'sent' => true,
                'test_mode' => true,
                'otp' => $code,
                'expires_in' => 300,
            ]);
        }

        try {
            $smsGateway->send($phone, $message);
        } catch (RuntimeException $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json(
                ['message' => 'Unable to send OTP. Please try again later.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return response()->json(['sent' => true]);
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'otp' => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);
        $otp = preg_replace('/\s+/', '', (string) $validated['otp']);

        if (! $this->looksLikePhone($phone)) {
            return response()->json(
                ['message' => 'Invalid phone number.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var SmsOtp|null $record */
        $record = SmsOtp::query()
            ->where('purpose', 'register')
            ->where('phone', $phone)
            ->active()
            ->latest('id')
            ->first();

        if (! $record) {
            return response()->json(
                ['message' => 'OTP expired or not found.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($record->attempts >= 5) {
            return response()->json(
                ['message' => 'Too many attempts. Please request a new code.'],
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        $record->increment('attempts');

        if (! Hash::check($otp, (string) $record->code_hash)) {
            return response()->json(
                ['message' => 'Invalid OTP.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $record->forceFill(['consumed_at' => now()])->save();

        $request->session()->put('register.phone_verified', [
            'phone' => $phone,
            'verified_at' => now()->toIso8601String(),
            'expires_at' => now()->addMinutes(30)->toIso8601String(),
        ]);

        return response()->json(['verified' => true]);
    }

    private function normalizePhone(string $phone): string
    {
        $trimmed = trim($phone);

        // Keep leading +, strip everything else non-digit
        if (Str::startsWith($trimmed, '+')) {
            return '+'.preg_replace('/\D+/', '', substr($trimmed, 1));
        }

        return preg_replace('/\D+/', '', $trimmed);
    }

    private function looksLikePhone(string $phone): bool
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $len = strlen((string) $digits);

        // E.164 max is 15 digits; minimum varies by country but <8 is almost always invalid.
        return $len >= 8 && $len <= 15;
    }
}
