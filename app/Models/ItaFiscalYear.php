<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItaFiscalYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'name',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function topics(): HasMany
    {
        return $this->hasMany(ItaMoitTopic::class, 'fiscal_year_id');
    }

    public function subTopics(): HasMany
    {
        return $this->hasMany(ItaMoitSubTopic::class, 'fiscal_year_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ItaDocument::class, 'fiscal_year_id');
    }
}
