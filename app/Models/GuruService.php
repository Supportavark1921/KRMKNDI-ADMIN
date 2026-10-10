<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuruService extends Model
{
    use SoftDeletes;

    protected $table = 'guru_services';

    protected $fillable = ['guru_id', 'service_id', 'pricing', 'pooja_samagri', 'status'];

    protected $casts = [
        'pricing'       => 'array',
        'pooja_samagri' => 'array',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function amount(): ?int
    {
        return $this->pricing['amount'] ?? null;
    }

    public function currency(): string
    {
        return $this->pricing['currency'] ?? 'INR';
    }

    public function discountAmount(): ?int
    {
        return $this->pricing['discount_amount'] ?? null;
    }

    public function samagri(): array
    {
        return $this->pooja_samagri ?? [];
    }
}
