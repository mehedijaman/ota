<?php

namespace Modules\Sms\Services;

use Illuminate\Support\Facades\DB;
use Modules\Sms\Models\SmsMessage;
use Throwable;

class SmsSendingService
{
    public function __construct(
        protected SmsGateway $gateway,
        protected SmsWalletService $wallet,
        protected SmsPartsService $partsService,
    ) {}

    /**
     * @param  array<string,mixed>  $meta
     */
    public function sendNow(int $tenantId, ?int $userId, string $type, string $toPhone, string $message, array $meta = []): SmsMessage
    {
        $parts = $this->partsService->parts($message);
        $costCredits = max(1, (int) $parts);

        /** @var SmsMessage $sms */
        $sms = DB::transaction(function () use ($tenantId, $userId, $type, $toPhone, $message, $parts, $costCredits, $meta) {
            $sms = SmsMessage::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'type' => $type,
                'to_phone' => $toPhone,
                'message' => $message,
                'parts' => $parts,
                'cost_credits' => $costCredits,
                'status' => 'queued',
                'queued_at' => now(),
                'meta' => $meta,
            ]);

            $this->wallet->reserveForMessage($tenantId, $sms, $costCredits);

            return $sms;
        });

        try {
            $response = $this->gateway->send($toPhone, $message);

            DB::transaction(function () use ($sms, $response) {
                $sms->status = 'sent';
                $sms->sent_at = now();
                $sms->provider_response = $response;
                $sms->save();

                $this->wallet->commitReservations($sms);
            });
        } catch (Throwable $e) {
            DB::transaction(function () use ($sms, $e) {
                $sms->status = 'failed';
                $sms->error_message = $e->getMessage();
                $sms->save();

                $this->wallet->releaseReservations($sms);
            });
        }

        return $sms->refresh();
    }
}
