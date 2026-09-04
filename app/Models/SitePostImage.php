<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePostImage extends Model
{
    use HasFactory;

    protected $fillable = ['site_post_id', 'image_path', 'caption', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(SitePost::class, 'site_post_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
