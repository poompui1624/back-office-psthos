<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ItaDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fiscal_year_id',
        'main_topic_id',
        'sub_topic_id',
        'title',
        'description',
        'file_original_name',
        'file_path',
        'file_mime',
        'file_extension',
        'file_size',
        'uploaded_by',
        'is_public',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
    ];

    protected $appends = [
        'file_url',
        'file_size_human',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(ItaFiscalYear::class, 'fiscal_year_id');
    }

    public function mainTopic(): BelongsTo
    {
        return $this->belongsTo(ItaMoitTopic::class, 'main_topic_id');
    }

    public function subTopic(): BelongsTo
    {
        return $this->belongsTo(ItaMoitSubTopic::class, 'sub_topic_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
