<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'description',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = 0)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? (float) $setting->value : $default;
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray()
    {
        return self::pluck('value', 'key')->map(function ($value) {
            return (float) $value;
        })->toArray();
    }

    /**
     * Update or create a setting
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}

