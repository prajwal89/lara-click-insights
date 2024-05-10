<?php

namespace Prajwal89\LaraClickInsights;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClickService
{
    public function recordImpressions(array $clickableStrings)
    {
        collect($clickableStrings)->map(function ($clickableString) {

            $clickAble = $this->resolveClickAble($clickableString);

            if (empty($clickAble)) {
                Log::info('Not able to resolve clickable: ' . $clickableString);

                return;
            }

            $variation = last(explode(':', $clickableString));

            $clickAble->impressions()->firstOrCreate([
                'date' => now()->toDateString(),
                'variation' => $variation,
            ])->increment('impressions');

            return $clickAble;
        });
    }

    public function recordClick(string $clickableString, string $sessionId)
    {
        $clickAble = $this->resolveClickAble($clickableString);
        
        $variation = last(explode(':', $clickableString));

        // // * Do not record duplicate clicks if happened before 6 hours
        // $cacheKey = $sessionId . '-clicked-' . $clickableString;

        // if (Cache::has($cacheKey)) {
        //     return false;
        // } else {
        //     Cache::put($cacheKey, 1, now()->addHours(6));
        // }

        $clickAble->impressions()->firstOrCreate([
            'date' => now()->toDateString(),
            'variation' => $variation,
        ])->increment('clicks');
    }

    // resolve string like User:322  or 'App\\Models\\User:23'
    public function resolveClickAble(string $clickAbleString): ?Model
    {
        if (!str_contains($clickAbleString, ':')) {
            return null;
        }

        [$clickableModel, $clickableId] = explode(':', trim($clickAbleString));

        if (config('lara-click-insights.use_short_model_names')) {
            $clickableModel = 'App\\Models\\' . $clickableModel;
        }

        if (!class_exists($clickableModel)) {
            return null;
        }

        return $clickableModel::findOrFail($clickableId);
    }
}
