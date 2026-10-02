<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes;

    // Allowed role values: admin | manager | support | guruji | vendor | user
    public const ROLES = ['admin', 'manager', 'support', 'guruji', 'vendor', 'user'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status', // active | suspended
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Role helpers (primary-type shortcuts) ────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isGuruji(): bool
    {
        return $this->role === 'guruji';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isEndUser(): bool
    {
        return $this->role === 'user';
    }

    public function hasRoleType(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }
}
