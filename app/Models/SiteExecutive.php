<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteExecutive extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'position', 'photo_path', 'phone', 'email',
        'sort_order', 'is_featured', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        /*
            Only one executive appears at the top of the homepage. Enforced here
            rather than in the form, because the rule has to hold however the row
            is written — a second form, a seeder, or tinker would otherwise leave
            the page with two directors and no say in which one it shows.
        */
        static::saved(function (self $executive) {
            if (! $executive->is_featured) {
                return;
            }

            static::query()
                ->whereKeyNot($executive->getKey())
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }
}
