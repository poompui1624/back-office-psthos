<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePostFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_post_id', 'title', 'file_path', 'file_original_name',
        'file_mime', 'file_extension', 'file_size', 'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'download_count' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(SitePost::class, 'site_post_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/'.$this->file_path) : null;
    }

    public function getFileSizeHumanAttribute(): string
    {
        return human_file_size($this->file_size);
    }

    /**
     * A name for the download, falling back to the file it came from.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->title ?: $this->file_original_name;
    }

    /**
     * The icon that best describes the file to someone scanning a list.
     */
    public function getIconAttribute(): string
    {
        return match (mb_strtolower((string) $this->file_extension)) {
            'xls', 'xlsx', 'csv' => 'chart',
            'jpg', 'jpeg', 'png', 'webp', 'gif' => 'box',
            default => 'document',
        };
    }
}
