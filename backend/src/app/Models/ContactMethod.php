<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'value',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * 所屬業主
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * 聯繫方式類型標籤
     */
    public function getTypeLabel(): string
    {
        $labels = [
            'phone' => '電話',
            'mobile' => '手機',
            'email' => 'Email',
            'line' => 'LINE',
            'wechat' => 'WeChat',
            'telegram' => 'Telegram',
            'other' => '其他',
        ];

        return $labels[$this->type] ?? $this->type;
    }
}
