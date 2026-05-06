<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItaMoitTopic extends Model
{
    protected $fillable = [
        'fiscal_year_id',
        'indicator_no',
        'indicator_title',
        'code',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'indicator_no' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(ItaFiscalYear::class, 'fiscal_year_id');
    }

    public function subTopics(): HasMany
    {
        return $this->hasMany(ItaMoitSubTopic::class, 'main_topic_id')
            ->orderBy('sort_order')
            ->orderBy('code');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ItaDocument::class, 'main_topic_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
