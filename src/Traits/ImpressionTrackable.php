<?php

namespace Prajwal89\LaraClickInsights\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Prajwal89\LaraClickInsights\Models\Impression;

// todo add helper methods
// average ctr
// average ctr for time frame
trait ImpressionTrackable
{
    public function trackingAttribute()
    {
        $parts = [get_class($this), $this->getKey()];

        $clickableDataAttribute = sprintf('data-clickable="%s:%s"', ...$parts);

        return $clickableDataAttribute;
    }

    public function impressions(): MorphMany
    {
        return $this->morphMany(Impression::class, 'impressionable');
    }
}
