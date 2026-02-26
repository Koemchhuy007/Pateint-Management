<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value'];

    /** Retrieve a setting value, with an optional default. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    /** Store or update a setting value. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Return all settings as a flat key → value array. */
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->all();
    }
}
