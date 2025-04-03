<?php

namespace Prajwal89\LaraClickInsights\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Prajwal89\LaraClickInsights\Http\Requests\ClickableFormDataRequest;
use Prajwal89\LaraClickInsights\Jobs\RecordEventJob;
use Prajwal89\LaraClickInsights\TrackEventService;
use Prajwal89\LaraClickInsights\Traits\ApiResponser;

class TrackEventsController extends Controller
{
    use ApiResponser;

    public function __construct(public TrackEventService $trackEventService)
    {
        $middlewareClass = config('lara-click-insights.middleware');

        if (!empty($middlewareClass) && class_exists($middlewareClass)) {
            $this->middleware($middlewareClass);
        }
    }

    public function __invoke(ClickableFormDataRequest $request)
    {
        $validatedData = $request->validated();

        if (config('lara-click-insights.queue_jobs')) {
            dispatch(new RecordEventJob(
                requestData: $validatedData,
                sessionId: session()->getId()
            ));
        } else {
            $this->trackEventService->recordImpressions($validatedData['clickables']);

            if (! empty($validatedData['clicked_on'])) {
                $this->trackEventService->recordClick($validatedData['clicked_on'], session()->getId());
            }
        }

        return $this->successResponse();
    }
}
