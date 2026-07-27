<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use Translatable;
    protected $with = ['translations'];
    protected $translatedAttributes = ['value'];

    protected $fillable = ['key', 'is_translatable', 'plain_value'];
    protected $casts = [
        'is_translatable' => 'boolean',
        'days_work' => 'array',

    ];

    public static function setMany($setting)
    {
        foreach ($setting as $key => $value) {
            self::set($key, $value);
            // use self becuse function static and set function must static , if public use $this
        }
    }

    public static function set($key, $value)
    {
        if ($key === 'translatable') {
            return static::setTranslatableSettings($value);
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        static::updateOrCreate(['key' => $key], ['plain_value' => $value]);
    }

    public static function setTranslatableSettings($setting = [])
    {
        foreach ($setting as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                [
                    'is_translatable' => true,
                    'value' => $value

                ]
            );
        }
    }

    public function getFinalValueAttribute()
    {
        $imageKeys = [
            'store_logo',
            'bannerLink',
        ];

        if ($this->is_translatable) {
            return $this->value;
        }

        $value = $this->plain_value;

        if (in_array($this->key, $imageKeys) && $value) {
            return asset('uploads/settings/' . $value);
        }

        if ($value && $this->isJson($value)) {
            return json_decode($value, true);
        }

        return $value;
    }


    protected function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

public static function getFloat(string $key, float $default = 0.0): float
{
    $value = self::where('key', $key)->value('plain_value');
    $value = trim($value ?? ''); // إذا كان null يحوله لنص فارغ

    return is_numeric($value) ? (float) $value : $default;
}




/**
 * تجيب قيمة نصية من الإعدادات
 */
public static function getString(string $key, string $default = ''): string
{
    $value = self::where('key', $key)->value('plain_value');
    return $value !== null ? trim($value) : $default;
}
}
