<?php

namespace Modules\Sms\Services;

use Modules\Sms\Models\SmsGateway;
use RuntimeException;
use Xenon\LaravelBDSms\Sender;

class SmsGatewayService
{
    /**
     * Send SMS using the default active gateway or a specific one.
     *
     * @return array<string, mixed>
     */
    public function send(string $phone, string $message, ?int $gatewayId = null): array
    {
        if ($gatewayId !== null) {
            $gateway = SmsGateway::query()
                ->where('is_active', true)
                ->find($gatewayId);
        } else {
            $gateway = SmsGateway::getActive();
        }

        if (! $gateway) {
            throw new RuntimeException('No active SMS gateway configured.');
        }

        return $this->sendWithGateway($gateway, $phone, $message);
    }

    /**
     * Send SMS using a specific gateway.
     *
     * @return array<string, mixed>
     */
    public function sendWithGateway(SmsGateway $gateway, string $phone, string $message): array
    {
        $providerClass = (string) $gateway->provider_class;
        $credentials = (array) ($gateway->credentials ?? []);

        return $this->sendWithProvider($providerClass, $credentials, $phone, $message);
    }

    /**
     * Send SMS using provider class and credentials directly.
     *
     * @param  array<string, mixed>  $credentials
     * @return array<string, mixed>
     */
    public function sendWithProvider(string $providerClass, array $credentials, string $phone, string $message): array
    {
        $sender = $this->getSenderInstance();
        $sender->setProvider($providerClass);
        $sender->setMobile($phone);
        $sender->setMessage($message);
        $sender->setQueue(false);
        $sender->setConfig($credentials);

        $maxAttempts = (int) config('sms.retry.max_attempts', 3);
        $backoffBase = (int) config('sms.retry.backoff_seconds', 2);
        $maxBackoff = (int) config('sms.retry.max_backoff_seconds', 30);
        $useJitter = (bool) config('sms.retry.jitter', true);

        $attempt = 0;
        $lastException = null;

        while (++$attempt <= $maxAttempts) {
            try {
                $status = $sender->send();

                if (! is_array($status)) {
                    return ['status' => $status];
                }

                return $status;
            } catch (\Throwable $e) {
                $lastException = $e;

                if ($attempt >= $maxAttempts) {
                    break;
                }

                $delay = $backoffBase * (2 ** ($attempt - 1));
                if ($delay > $maxBackoff) {
                    $delay = $maxBackoff;
                }

                if ($useJitter) {
                    $jitter = random_int(0, (int) max(0, floor($delay * 0.2)));
                    $delay = $delay + $jitter;
                }

                if (! app()->runningUnitTests()) {
                    sleep((int) $delay);
                }
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        return ['status' => 'failed'];
    }

    /**
     * Return an instance of the Sender.
     *
     * @return mixed
     */
    protected function getSenderInstance()
    {
        return Sender::getInstance();
    }
}
