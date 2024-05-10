<?php

use Illuminate\Support\Facades\Route;
use Prajwal89\LaraClickInsights\Controllers\TrackEventsController;

// todo this path should be dynamic
Route::post('/lara-click-insights', TrackEventsController::class);
