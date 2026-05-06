<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItaMoitSubTopic extends Model
{
    protected $fillable = [
        'fiscal_year_id',
        'main_topic_id',
        'code',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(ItaFiscalYear::class, 'fiscal_year_id');
    }

    public function mainTopic(): BelongsTo
    {
        return $this->belongsTo(ItaMoitTopic::class, 'main_topic_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ItaDocument::class, 'sub_topic_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
