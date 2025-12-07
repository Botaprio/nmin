<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YoutubeVideo extends Model
{
    protected $fillable = [
        'card_id',
        'youtube_video_id',
        'title',
        'description',
        'status',
        'published_at',
        'view_count',
        'like_count',
        'comment_count',
        'watch_time_minutes',
        'analytics_last_updated',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'watch_time_minutes' => 'integer',
        'analytics_last_updated' => 'datetime',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function getYoutubeUrlAttribute(): ?string
    {
        if (!$this->youtube_video_id) {
            return null;
        }
        return "https://www.youtube.com/watch?v={$this->youtube_video_id}";
    }
}
