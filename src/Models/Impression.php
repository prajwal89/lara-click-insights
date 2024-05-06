<?php

namespace Prajwal89\LaraClickInsights\Models;

use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    protected $table = 'impressions';

    public $timestamps = false;

    protected $fillable = [
        'impressionable_type',
        'impressionable_id',
        'impressions',
        'clicks',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function impressionable()
    {
        return $this->morphTo();
    }
}
