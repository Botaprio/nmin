<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Card extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'column_id',
        'board_id',
        'title',
        'description',
        'position',
        'priority',
        'due_date',
        'start_date',
        'estimated_hours',
        'video_idea',
        'script_notes',
        'animation_prompts',
        'music_notes',
        'cover_image',
        'created_by',
    ];

    protected $casts = [
        'position' => 'integer',
        'due_date' => 'date',
        'start_date' => 'date',
        'estimated_hours' => 'integer',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CardAssignment::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'card_assignments')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function googleDriveFiles(): HasMany
    {
        return $this->hasMany(GoogleDriveFile::class);
    }

    public function youtubeVideo(): HasOne
    {
        return $this->hasOne(YoutubeVideo::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
