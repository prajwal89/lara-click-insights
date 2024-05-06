<?php
return [
    'use_short_model_names' => true,

    /**
     * This will add event recording logic to queues, as it may be a resource-intensive task.
     * Before enabling this feature, make sure you have set up the jobs table.
     * This will dispatch `Prajwal89\LaraClickInsights\Jobs\RecordEventJob`.
     */

    'queue_jobs' => true,
];
