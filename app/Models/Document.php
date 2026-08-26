<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tender_id',
        'proposal_id',
        'category_id',
        'name',
        'original_name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'extension',
        'version',
        'description',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'version'   => 'integer',
        ];
    }

    // Relationships

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this->id);
    }

    public function isPreviewable(): bool
    {
        return in_array(strtolower($this->extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
    }

    public function exists(): bool
    {
        return Storage::disk('private')->exists($this->file_path);
    }
}
