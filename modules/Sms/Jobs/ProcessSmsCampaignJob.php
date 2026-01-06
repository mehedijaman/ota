<?php

namespace Modules\Sms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Sms\Models\SmsCampaign;
use Modules\Sms\Models\SmsCampaignRecipient;
use Modules\Sms\Services\SmsGatewayService;

class ProcessSmsCampaignJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 3600;

    public function __construct(public SmsCampaign $campaign) {}

    public function handle(SmsGatewayService $smsGateway): void
    {
        $campaign = $this->campaign;

        if ($campaign->status !== 'pending') {
            return;
        }

        $campaign->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        $message = $campaign->message;
        $sent = 0;
        $failed = 0;

        $recipients = SmsCampaignRecipient::query()
            ->where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->cursor();

        foreach ($recipients as $recipient) {
            try {
                $response = $smsGateway->send($recipient->phone, $message);

                $recipient->update([
                    'status' => 'sent',
                    'provider_response' => $response,
                    'sent_at' => now(),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $failed++;

                Log::warning('SMS Campaign recipient failed', [
                    'campaign_id' => $campaign->id,
                    'phone' => $recipient->phone,
                    'error' => $e->getMessage(),
                ]);
            }

            if (($sent + $failed) % 100 === 0) {
                $campaign->update([
                    'sent_count' => $sent,
                    'failed_count' => $failed,
                ]);
            }
        }

        $campaign->update([
            'sent_count' => $sent,
            'failed_count' => $failed,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Log::info('SMS Campaign completed', [
            'campaign_id' => $campaign->id,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);

        Log::error('SMS Campaign job failed', [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
