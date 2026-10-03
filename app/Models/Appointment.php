<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'guru_id', 'service_id', 'service',
        'name', 'appointment_date', 'appointment_time',
        'phone', 'notes', 'status',
        'total_amount', 'payment_id', 'samagri',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'total_amount'     => 'decimal:2',
            'samagri'          => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
