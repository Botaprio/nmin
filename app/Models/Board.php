<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_template',
        'settings',
    ];

    protected $casts = [
        'is_template' => 'boolean',
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('position');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function scenes(): HasMany
    {
        return $this->hasMany(Scene::class);
    }

    /**
     * Get board completion percentage based on completed scenes
     */
    public function getCompletionPercentage(): int
    {
        $totalScenes = $this->scenes()->count();
        
        if ($totalScenes === 0) {
            return 0;
        }

        $completedScenes = $this->scenes->filter(function ($scene) {
            return $scene->isComplete();
        })->count();

        return round(($completedScenes / $totalScenes) * 100);
    }
}
