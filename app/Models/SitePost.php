<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SitePost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'news' => 'ข่าวประชาสัมพันธ์',
        'activity' => 'ภาพกิจกรรม',
        'knowledge' => 'ความรู้สู่ประชาชน',
    ];

    protected $fillable = [
        'category', 'title', 'slug', 'excerpt', 'body', 'cover_image_path',
        'published_at', 'is_published', 'is_pinned', 'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_pinned' => 'boolean',
        'view_count' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(SitePostImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SitePostFile::class)->orderBy('sort_order')->orderBy('id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Posts a visitor may see.
     *
     * A post is live only once it is marked published and its publish time has
     * arrived, so an editor can prepare one and let it appear on its own.
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? asset('storage/'.$this->cover_image_path) : null;
    }

    /**
     * A URL-safe slug that keeps Thai text.
     *
     * Str::slug drops any character outside the latin range, which turns
     * "ประกาศวันหยุดสงกรานต์ 2569" into "2569" and two Thai titles ending in
     * the same year into a collision. Thai characters are kept here so the
     * link stays readable and distinct.
     */
    public static function slugFor(string $title, ?int $ignoreId = null): string
    {
        $slug = mb_strtolower(trim($title));

        // Keep Thai, latin letters, digits; everything else becomes a separator.
        $slug = preg_replace('/[^\p{Thai}\p{Latin}0-9]+/u', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        if ($slug === '') {
            $slug = 'post-'.now()->format('YmdHis');
        }

        $base = mb_substr($slug, 0, 180);
        $candidate = $base;
        $suffix = 2;

        while (self::withTrashed()
            ->where('slug', $candidate)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
