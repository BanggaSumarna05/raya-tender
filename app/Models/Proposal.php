<?php

namespace App\Models;

use App\Enums\DeadlineStatus;
use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tender_id',
        'code',
        'title',
        'pic_id',
        'backup_pic_id',
        'description',
        'bid_value',
        'status',
        'current_version',
        'deadline',
        'valid_until',
        'submitted_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline'      => 'date',
            'valid_until'   => 'date',
            'submitted_at'  => 'datetime',
            'bid_value'     => 'decimal:2',
            'status'        => ProposalStatus::class,
        ];
    }

    // Relationships

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function backupPic()
    {
        return $this->belongsTo(User::class, 'backup_pic_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function versions()
    {
        return $this->hasMany(ProposalVersion::class)->orderBy('version_number', 'asc');
    }

    // Scopes

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhereHas('tender', fn($t) => $t->where('title', 'like', "%{$search}%"))
              ->orWhereHas('pic', fn($u) => $u->where('name', 'like', "%{$search}%"));
        });
    }

    // Accessors

    public function getDeadlineStatusAttribute(): ?DeadlineStatus
    {
        if ($this->deadline === null) {
            return null;
        }

        return DeadlineStatus::calculate($this->deadline->toDateTime());
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if ($this->deadline === null) {
            return null;
        }

        return (int) now()->diffInDays($this->deadline, false);
    }
}
