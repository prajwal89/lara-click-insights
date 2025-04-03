<?php

namespace Prajwal89\LaraClickInsights;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrackEventService
{
    public function recordImpressions(array $clickableStrings): void
    {
        collect($clickableStrings)->map(function ($clickableString) {

            $clickAble = $this->resolveClickAble($clickableString);

            if (empty($clickAble)) {
                Log::info('Not able to resolve clickable: ' . $clickableString);

                return;
            }

            $clickAble->impressions()->firstOrCreate([
                'date' => now()->toDateString(),
                'variation' => last(explode(':', $clickableString)),
            ])->increment('impressions');

            return $clickAble;
        });
    }

    public function recordClick(string $clickableString, string $sessionId): void
    {
        $clickAble = $this->resolveClickAble($clickableString);

        if (config('lara-click-insights.avoid_tracking_quick_clicks')) {
            $cacheKey = $sessionId . '-clicked-' . $clickableString;

            if (Cache::has($cacheKey)) {
                // dd('skipped');

                // user has clicked on this before min_gap_between_clicks_in_sec
                return;
            } else {
                Cache::put($cacheKey, 1, now()->addSecond(config('lara-click-insights.min_gap_between_clicks_in_sec')));
            }
        }

        $clickAble->impressions()->firstOrCreate([
            'date' => now()->toDateString(),
            'variation' => last(explode(':', $clickableString)),
        ])->increment('clicks');
    }

    /**
     * resolve string like User:322 or 'App\\Models\\User:23' to model
     */
    public function resolveClickAble(string $clickAbleString): ?Model
    {
        if (! str_contains($clickAbleString, ':')) {
            return null;
        }

        [$clickableModel, $clickableId] = explode(':', trim($clickAbleString));

        if (config('lara-click-insights.use_short_model_names')) {
            $clickableModel = 'App\\Models\\' . $clickableModel;
        }

        if (! class_exists($clickableModel)) {
            return null;
        }

        return $clickableModel::findOrFail($clickableId);
    }
}
