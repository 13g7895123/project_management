<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get setting value with appropriate type casting
     */
    public function getValue()
    {
        switch ($this->type) {
            case 'boolean':
                return $this->value === '1' || $this->value === 'true';
            case 'json':
                return json_decode($this->value, true);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            default:
                return $this->value;
        }
    }

    /**
     * Set setting value with appropriate type conversion
     */
    public function setValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->value = $value ? '1' : '0';
                break;
            case 'json':
                $this->value = json_encode($value);
                break;
            default:
                $this->value = (string) $value;
        }
        
        return $this;
    }

    /**
     * Static method to get a setting value
     */
    public static function getSetting($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Static method to set a setting value
     */
    public static function setSetting($key, $value, $type = 'string', $description = null)
    {
        $setting = static::firstOrCreate(['key' => $key], [
            'type' => $type,
            'description' => $description,
        ]);
        
        $setting->type = $type;
        $setting->setValue($value);
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();
        
        return $setting;
    }

    /**
     * Get all settings as an associative array
     */
    public static function getAllSettings()
    {
        $settings = static::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->getValue();
        }
        
        return $result;
    }
}