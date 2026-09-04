<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitePage extends Model
{
    use HasFactory;

    /**
     * The pages the homepage knows how to place, and their labels.
     *
     * @var array<string, string>
     */
    public const KEYS = [
        'history' => 'ประวัติโรงพยาบาล',
        'vision' => 'วิสัยทัศน์ พันธกิจ',
        'structure' => 'โครงสร้างผู้บริหาร',
    ];

    protected $fillable = ['key', 'title', 'body', 'image_path', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
