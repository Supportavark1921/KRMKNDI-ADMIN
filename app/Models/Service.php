<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'duration_minutes', 'price', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function ensureDefaults(): void
    {
        foreach ([
            ['name' => 'Kundli consultation', 'description' => 'Personal guidance based on your birth chart.', 'duration_minutes' => 60],
            ['name' => 'Puja booking', 'description' => 'Sacred puja for your family, home, or occasion.', 'duration_minutes' => 120],
            ['name' => 'Marriage matching', 'description' => 'Thoughtful compatibility and kundli matching.', 'duration_minutes' => 60],
            ['name' => 'Griha pravesh / Muhurat', 'description' => 'Auspicious timing for important beginnings.', 'duration_minutes' => 90],
        ] as $service) {
            static::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
