<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'how_we_met',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 所屬用戶
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 聯繫方式
     */
    public function contactMethods(): HasMany
    {
        return $this->hasMany(ContactMethod::class);
    }

    /**
     * 專案
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * 主要聯繫方式
     */
    public function primaryContact()
    {
        return $this->contactMethods()->where('is_primary', true)->first();
    }

    /**
     * 專案數量統計
     */
    public function getProjectsCountAttribute()
    {
        return $this->projects()->count();
    }
}
