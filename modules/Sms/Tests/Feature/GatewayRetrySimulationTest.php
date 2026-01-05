<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Sms\Services\SmsGateway;

uses(RefreshDatabase::class);

it('simulates a retry/backoff wrapper that retries transient failures', function () {
    $state = (object) ['attempts' => 0];

    // inner gateway that fails twice then succeeds
    $inner = new class($state) extends SmsGateway
    {
        public $state;

        public function __construct($state)
        {
            $this->state = $state;
        }

        public function send(string $phone, string $message): array
        {
            $this->state->attempts++;
            if ($this->state->attempts <= 2) {
                throw new RuntimeException('Transient');
            }

            return ['status' => 'sent'];
        }
    };

    // retrying wrapper used in test only
    $wrapper = new class($inner) extends SmsGateway
    {
        public function __construct($inner)
        {
            $this->inner = $inner;
        }

        public function send(string $phone, string $message): array
        {
            $max = 4;
            $delay = 0; // no actual sleep in tests
            $i = 0;
            do {
                try {
                    return $this->inner->send($phone, $message);
                } catch (RuntimeException $e) {
                    $i++;
                    if ($i >= $max) {
                        throw $e;
                    }
                }
            } while (true);
        }
    };

    $res = $wrapper->send('01700000000', 'hello');

    expect($res['status'])->toBe('sent');
    expect($state->attempts)->toBe(3);
});
