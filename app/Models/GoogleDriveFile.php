<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleDriveFile extends Model
{
    protected $fillable = [
        'card_id',
        'drive_file_id',
        'name',
        'mime_type',
        'file_type',
        'size',
        'web_view_link',
        'web_content_link',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
