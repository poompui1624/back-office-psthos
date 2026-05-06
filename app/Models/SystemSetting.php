<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'label',
        'value',
        'type',
        'description',
        'options',
        'is_public',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];
}
