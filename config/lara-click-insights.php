<?php

return [

    /**
     * event data will be sent here
     */
    'endpoint' => '/api/lara-click-insights',
    
    /**
     * impressions table name
     */
    'table' => 'impressions',

    /*
    |--------------------------------------------------------------------------
    | Use Short Model Names
    |--------------------------------------------------------------------------
    |
    | When set to true, it minimizes the attribute value by using short model names
    | instead of full model names like 'App\Models\User', it will use only 'User'.
    |
    */
    'use_short_model_names' => true,

    /*
    |--------------------------------------------------------------------------
    | Queue Jobs for Event Recording
    |--------------------------------------------------------------------------
    |
    | Before enabling this feature, make sure you have set up the Laravel jobs table.
    | This will add event recording logic to queues, as it may be a resource-intensive task.
    | This will dispatch `Prajwal89\LaraClickInsights\Jobs\RecordEventJob`.
    |
    */
    'queue_jobs' => false,

    /*
    |--------------------------------------------------------------------------
    | Avoid Tracking Quick Clicks
    |--------------------------------------------------------------------------
    |
    | When set to true, this feature prevents tracking of the same click from the
    | same user in quick intervals. Ensure that laravel caching is enabled for this feature to work.
    |
    */
    'avoid_tracking_quick_clicks' => true,

    /*
    |--------------------------------------------------------------------------
    | Minimum Gap Between Clicks (in seconds)
    |--------------------------------------------------------------------------
    |
    | Determines the minimum time gap required between consecutive clicks
    | to avoid tracking the same click from the same user in quick intervals.
    | Default value is 14400 seconds (4 hours).
    |
    */
    'min_gap_between_clicks_in_sec' => 60 * 60 * 4,

    /**
     * send data to server on every x seconds
     */
    'polling_delay_in_sec' => 3,

    /**
     * value between 0 to 1 how much percentage of clickable element should be in users viewport to be taken as
     * impression
     */
    'intersection_threshold' => 0.5,
];
