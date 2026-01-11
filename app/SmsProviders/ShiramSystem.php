<?php

namespace App\SmsProviders;

use App\Jobs\SendShiramSystemSmsJob;
use Illuminate\Support\Facades\Http;
use App\Handler\RenderException;
use App\Sender;

class ShiramSystem extends AbstractProvider
{
    private const DEFAULT_ENDPOINT = 'https://smsapi.shiramsystem.com/user_api/';

    /**
     * ShiramSystem constructor.
     */
    public function __construct(Sender $sender)
    {
        $this->senderObject = $sender;
    }

    /**
     * Send Request To API and Send Message
     *
     * Shiram expects application/x-www-form-urlencoded POST fields.
     */
    public function sendRequest()
    {
        $mobile = $this->senderObject->getMobile();
        $text = (string) $this->senderObject->getMessage();
        $config = (array) $this->senderObject->getConfig();
        $queue = (bool) $this->senderObject->getQueue();
        $queueName = $this->senderObject->getQueueName();
        $tries = (int) $this->senderObject->getTries();
        $backoff = (int) $this->senderObject->getBackoff();

        $endpoint = (string) ($config['endpoint'] ?? self::DEFAULT_ENDPOINT);
        $mask = (string) ($config['mask'] ?? 'Non-Masking');

        $mobiles = is_array($mobile) ? $mobile : [$mobile];
        $mobiles = array_values(array_map(fn ($m) => $this->normalizeMobile((string) $m), $mobiles));

        if (count($mobiles) > 100) {
            throw new RenderException('Maximum 100 mobile numbers can be added in a request.');
        }

        $post = [
            'email' => (string) ($config['email'] ?? ''),
            'password' => (string) ($config['password'] ?? ''),
            'method' => 'send_sms',
            'mask' => $mask,
            'mobile' => $mobiles,
            'message' => $text,
        ];

        if ($queue) {
            dispatch((new SendShiramSystemSmsJob($endpoint, $post, $tries, $backoff))->onQueue($queueName));

            return true;
        }

        $response = Http::asForm()
            ->timeout(20)
            ->post($endpoint, $post);

        if (! $response->successful()) {
            throw new RenderException('Shiram SMS request failed (HTTP '.$response->status().'): '.$response->body());
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new RenderException('Shiram SMS returned invalid JSON: '.$response->body());
        }

        $status = $payload['status'] ?? null;
        $errorCode = $payload['error_code'] ?? null;

        $isFailed = ($status === false)
            || ($status === 'false')
            || ($errorCode !== null && (int) $errorCode !== 0);

        if ($isFailed) {
            $message = trim((string) ($payload['message'] ?? ''));

            if ((int) $errorCode === 111) {
                $message = $message !== ''
                    ? $message
                    : 'Shiram SMS: Message must be Bangla Unicode.';
            }

            if ($message === '') {
                $message = 'Shiram SMS failed'.($errorCode !== null ? ' (error '.$errorCode.').' : '.');
            }

            throw new RenderException($message);
        }

        $smsResult = $response->body();

        $data['number'] = $mobile;
        $data['message'] = $text;

        return $this->generateReport($smsResult, $data)->getContent();
    }

    /**
     * @throws RenderException
     */
    public function errorException(): void
    {
        $config = (array) $this->senderObject->getConfig();

        if (! array_key_exists('email', $config) || trim((string) $config['email']) === '') {
            throw new RenderException('email key is absent in configuration');
        }

        if (! array_key_exists('password', $config) || trim((string) $config['password']) === '') {
            throw new RenderException('password key is absent in configuration');
        }
    }

    private function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', trim($mobile));

        if ($digits === '') {
            return $digits;
        }

        if (str_starts_with($digits, '88')) {
            return $digits;
        }

        return '88'.$digits;
    }
}
