<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiCredit extends Model
{
    protected $fillable = [
        'service',
        'total_credits',
        'used_credits',
        'billing_period_start',
        'billing_period_end',
        'updated_by',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-calculate remaining credits
            $model->remaining_credits = $model->total_credits - $model->used_credits;
        });
    }

    protected $casts = [
        'total_credits' => 'integer',
        'used_credits' => 'integer',
        'remaining_credits' => 'integer',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->total_credits === 0) {
            return 0;
        }
        return ($this->used_credits / $this->total_credits) * 100;
    }

    public function getPercentageRemainingAttribute(): float
    {
        if ($this->total_credits === 0) {
            return 0;
        }
        return ($this->remaining_credits / $this->total_credits) * 100;
    }

    public function isLowCredits(int $threshold = 20): bool
    {
        return $this->getPercentageRemainingAttribute() < $threshold;
    }
}
