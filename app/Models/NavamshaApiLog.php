<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavamshaApiLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'endpoint', 'cache_key', 'feature', 'request_date',
        'latitude', 'longitude', 'timezone',
        'http_status', 'response_time_ms', 'success', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'request_date'     => 'date',
            'success'          => 'boolean',
            'response_time_ms' => 'integer',
            'http_status'      => 'integer',
        ];
    }
}
