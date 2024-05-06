<?php

namespace Prajwal89\LaraClickInsights\Controllers;

use Illuminate\Http\Request;
use Prajwal89\LaraClickInsights\ClickService;
use Prajwal89\LaraClickInsights\Requests\ClickableFormDataRequest;
use Prajwal89\LaraClickInsights\Traits\ApiResponser;

class TrackImpressionsController
{
    use ApiResponser;

    public function __construct(public ClickService $clickService)
    {
    }

    // todo add validation
    public function __invoke(Request $request)
    {
        // return $this->successResponse($request->all());

        $validatedData = $request->all();

        $this->clickService->recordImpressions($validatedData['clickables']);

        if (!empty($validatedData['clicked_on'])) {
            $this->clickService->recordClick($validatedData['clicked_on'], session()->getId());
        }

        return $this->successResponse($validatedData);
    }
}
