<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanchangCache extends Model
{
    protected $table = 'panchang_cache';

    protected $fillable = [
        'cache_key', 'date', 'latitude', 'longitude',
        'normalized_latitude', 'normalized_longitude', 'timezone',
        'location_name', 'feature', 'api_endpoint',
        'api_response', 'panchang_data', 'api_status',
        'api_version', 'fetched_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'panchang_data' => 'array',
            'fetched_at' => 'datetime',
            'expires_at' => 'datetime',
            'date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
            'normalized_latitude' => 'float',
            'normalized_longitude' => 'float',
            'timezone' => 'float',
        ];
    }

    public function isValid(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
