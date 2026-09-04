<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category', 'title', 'description', 'file_path', 'file_original_name',
        'file_mime', 'file_extension', 'file_size', 'published_at',
        'is_published', 'created_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'download_count' => 'integer',
    ];

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return config('site.document_categories', []);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Documents a visitor may see.
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category] ?? $this->category;
    }

    public function getFileSizeHumanAttribute(): string
    {
        return human_file_size($this->file_size);
    }

    public function getIconAttribute(): string
    {
        return match (mb_strtolower((string) $this->file_extension)) {
            'xls', 'xlsx', 'csv' => 'chart',
            'jpg', 'jpeg', 'png', 'webp' => 'box',
            default => 'document',
        };
    }
}
