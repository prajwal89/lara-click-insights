<?php

namespace Prajwal89\LaraClickInsights\Controllers;

use Illuminate\Http\Request;
use Prajwal89\LaraClickInsights\ClickService;
use Prajwal89\LaraClickInsights\Jobs\RecordEventJob;
use Prajwal89\LaraClickInsights\Traits\ApiResponser;

class TrackImpressionsController
{
    use ApiResponser;

    public function __construct(public ClickService $clickService)
    {
    }

    public function __invoke(Request $request)
    {
        $validatedData = $request->all();

        if (config('lara-click-insights.queue_jobs')) {
            dispatch(new RecordEventJob(requestData: $validatedData, sessionId: session()->getId()));
        } else {
            $this->clickService->recordImpressions($validatedData['clickables']);

            if (!empty($validatedData['clicked_on'])) {
                $this->clickService->recordClick($validatedData['clicked_on'], session()->getId());
            }
        }

        return $this->successResponse();
    }
}
