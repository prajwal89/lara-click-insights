<?php

declare(strict_types=1);

namespace Prajwal89\LaraClickInsights\Models;

use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'impressionable_type',
        'impressionable_id',
        'impressions',
        'clicks',
        'variation',
        'date',
    ];

    protected $casts = [
        'impressions' => 'integer',
        'clicks' => 'integer',
        'date' => 'date',
    ];

    public function getTable()
    {
        return config('lara-click-insights.table');
    }

    public function impressionable()
    {
        return $this->morphTo();
    }
}
