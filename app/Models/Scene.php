<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scene extends Model
{
    protected $fillable = [
        'board_id',
        'name',
        'description',
        'position',
        'color',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Check if all cards in this scene are in the "OK" column
     */
    public function isComplete(): bool
    {
        $cards = $this->cards;
        
        if ($cards->isEmpty()) {
            return false;
        }

        // Get the OK column for this board
        $okColumn = $this->board->columns()->where('name', 'OK')->first();
        
        if (!$okColumn) {
            return false;
        }

        // Check if all cards are in the OK column
        return $cards->every(function ($card) use ($okColumn) {
            return $card->column_id === $okColumn->id;
        });
    }

    /**
     * Get completion percentage (0-100)
     */
    public function getCompletionPercentage(): int
    {
        $totalCards = $this->cards()->count();
        
        if ($totalCards === 0) {
            return 0;
        }

        $okColumn = $this->board->columns()->where('name', 'OK')->first();
        
        if (!$okColumn) {
            return 0;
        }

        $completedCards = $this->cards()->where('column_id', $okColumn->id)->count();
        
        return round(($completedCards / $totalCards) * 100);
    }
}
