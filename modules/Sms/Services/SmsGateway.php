<?php

namespace Modules\Sms\Services;

use Modules\Sms\Models\SmsSetting;
use RuntimeException;
use Xenon\LaravelBDSms\Sender;

class SmsGateway
{
    /**
     * @return array<string,mixed>
     */
    public function send(string $phone, string $message): array
    {
        $default = SmsSetting::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (! $default) {
            $default = SmsSetting::query()->where('is_active', true)->first();
        }

        if (! $default) {
            throw new RuntimeException('SMS provider is not configured.');
        }

        $providerClass = (string) $default->provider_class;
        $credentials = (array) ($default->credentials ?? []);

        return $this->sendWith($providerClass, $credentials, $phone, $message);
    }

    /**
     * @param  array<string,mixed>  $credentials
     * @return array<string,mixed>
     */
    public function sendWith(string $providerClass, array $credentials, string $phone, string $message): array
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
                    $jitter = random_int(0, (int) max(0, floor($delay * 0.2))); // up to 20% jitter
                    $delay = $delay + $jitter;
                }

                // Avoid sleeping during unit tests to keep tests fast.
                if (! app()->runningUnitTests()) {
                    sleep((int) $delay);
                }
            }
        }

        // If we reach here, all attempts failed — rethrow the last exception for callers to handle.
        if ($lastException) {
            throw $lastException;
        }

        return ['status' => 'failed'];
    }

    /**
     * Return an instance of the Sender. Extracted to a method to allow tests
     * to override and provide a fake sender implementation.
     *
     * @return mixed
     */
    protected function getSenderInstance()
    {
        return Sender::getInstance();
    }
}
