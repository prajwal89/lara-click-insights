<?php

declare(strict_types=1);

namespace Prajwal89\LaraClickInsights\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Prajwal89\LaraClickInsights\Models\Impression;

// todo add helper methods
// average ctr
// average ctr for time frame
trait ImpressionTrackable
{
    public function trackingAttribute(string $variation = 'default'): string
    {
        if (!config('lara-click-insights.use_short_model_names')) {
            $modelClassName = get_class($this);
        } else {
            $modelClassName = str_replace('App\\Models\\', '', get_class($this));
        }

        $parts = [$modelClassName, $this->getKey(), $variation];

        $value = sprintf('%s:%s:%s', ...$parts);

        $attribute = 'data-clickable="'.$value.'"';

        return $attribute;
    }

    public function impressions(): MorphMany
    {
        return $this->morphMany(Impression::class, 'impressionable');
    }
}
