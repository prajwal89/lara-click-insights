<?php

use Illuminate\Support\Facades\Route;
use Prajwal89\LaraClickInsights\Http\Controllers\TrackEventsController;

Route::middleware('web')->group(function () {
    Route::post(config('lara-click-insights.endpoint'), TrackEventsController::class);
});
