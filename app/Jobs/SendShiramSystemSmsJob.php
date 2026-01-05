<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendShiramSystemSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The number of seconds to wait before retrying a job.
     */
    public int $backoff;

    /**
     * @param  array<string,mixed>  $post
     */
    public function __construct(
        public string $endpoint,
        public array $post,
        int $tries = 3,
        int $backoff = 60,
    ) {
        $this->tries = $tries;
        $this->backoff = $backoff;
    }

    public function handle(): void
    {
        Http::asForm()
            ->timeout(20)
            ->post($this->endpoint, $this->post);
    }
}
