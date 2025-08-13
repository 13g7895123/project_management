<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'description',
        'category',
        'amount',
        'contact_date',
        'start_date',
        'expected_completion_date',
        'completion_date',
        'payment_date',
        'status',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contact_date' => 'date',
        'start_date' => 'date',
        'expected_completion_date' => 'date',
        'completion_date' => 'date',
        'payment_date' => 'date',
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
     * 所屬業主
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * 專案類別標籤
     */
    public function getCategoryLabel(): string
    {
        $labels = [
            'website' => '網站',
            'script' => '腳本',
            'server' => '伺服器',
            'custom' => '自訂',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    /**
     * 專案狀態標籤
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending_evaluation' => '待評估',
            'contacted' => '已接洽',
            'in_progress' => '進行中',
            'completed' => '已完成',
            'paid' => '已收款',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * 優先級標籤
     */
    public function getPriorityLabel(): string
    {
        $labels = [
            'low' => '低',
            'medium' => '中',
            'high' => '高',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    /**
     * 格式化金額顯示
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'NT$' . number_format($this->amount);
    }

    /**
     * 專案進度百分比
     */
    public function getProgressPercentage(): int
    {
        switch ($this->status) {
            case 'pending_evaluation':
                return 10;
            case 'contacted':
                return 25;
            case 'in_progress':
                return 50;
            case 'completed':
                return 75;
            case 'paid':
                return 100;
            default:
                return 0;
        }
    }
}
