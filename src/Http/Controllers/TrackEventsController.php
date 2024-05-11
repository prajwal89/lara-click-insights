<?php

namespace Prajwal89\LaraClickInsights\Http\Controllers;

use Illuminate\Http\Request;
use Prajwal89\LaraClickInsights\Http\Requests\ClickableFormDataRequest;
use Prajwal89\LaraClickInsights\Jobs\RecordEventJob;
use Prajwal89\LaraClickInsights\TrackEventService;
use Prajwal89\LaraClickInsights\Traits\ApiResponser;

class TrackEventsController
{
    use ApiResponser;

    public function __construct(public TrackEventService $trackEventService)
    {
    }

    public function __invoke(ClickableFormDataRequest $request)
    {
        $validatedData = $request->all();

        if (config('lara-click-insights.queue_jobs')) {
            dispatch(new RecordEventJob(requestData: $validatedData, sessionId: session()->getId()));
        } else {
            $this->trackEventService->recordImpressions($validatedData['clickables']);

            if (! empty($validatedData['clicked_on'])) {
                $this->trackEventService->recordClick($validatedData['clicked_on'], session()->getId());
            }
        }

        return $this->successResponse();
    }
}
