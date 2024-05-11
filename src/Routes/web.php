<?php

use Illuminate\Support\Facades\Route;
use Prajwal89\LaraClickInsights\Controllers\TrackEventsController;

Route::post(config('lara-click-insights.endpoint'), TrackEventsController::class);
