<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'module',
        'attachable_type',
        'attachable_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeTextAttribute(): string
    {
        if ($this->file_size >= 1024 * 1024) {
            return number_format($this->file_size / 1024 / 1024, 2) . ' MB';
        }

        return number_format($this->file_size / 1024, 2) . ' KB';
    }
}
