<?php

declare(strict_types=1);

namespace Prajwal89\LaraClickInsights\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Prajwal89\LaraClickInsights\ClickService;

class RecordEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ClickService $clickService;

    public function __construct(
        public array $requestData,
        public string $sessionId
    ) {
        $this->clickService = new ClickService;
    }

    public function handle(): void
    {
        $this->clickService->recordImpressions($this->requestData['clickables']);

        if (!empty($this->requestData['clicked_on'])) {
            $this->clickService->recordClick($this->requestData['clicked_on'], $this->sessionId);
        }
    }
}
