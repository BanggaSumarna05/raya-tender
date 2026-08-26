<?php

namespace App\Models;

use App\Enums\DeadlineStatus;
use App\Enums\TenderPriority;
use App\Enums\TenderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'category_id',
        'code',
        'title',
        'description',
        'source',
        'source_reference',
        'pic_id',
        'backup_pic_id',
        'location',
        'estimated_value',
        'received_date',
        'submission_deadline',
        'project_start_date',
        'project_end_date',
        'status',
        'priority',
        'requirements',
        'notes',
        'no_bid_reason',
        'lost_reason',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submission_deadline' => 'datetime',
            'received_date'       => 'date',
            'project_start_date'  => 'date',
            'project_end_date'    => 'date',
            'estimated_value'     => 'decimal:2',
            'status'              => TenderStatus::class,
            'priority'            => TenderPriority::class,
        ];
    }

    // Relationships

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function category()
    {
        return $this->belongsTo(TenderCategory::class);
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

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(TenderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', TenderStatus::activeStatuses());
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"))
              ->orWhereHas('pic', fn($u) => $u->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeUpcomingDeadline($query, int $days = 30)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled'])
                     ->whereNotNull('submission_deadline')
                     ->where('submission_deadline', '>=', now())
                     ->where('submission_deadline', '<=', now()->addDays($days));
    }

    // Accessors

    public function getDeadlineStatusAttribute(): ?DeadlineStatus
    {
        return DeadlineStatus::calculate($this->submission_deadline?->toDateTime());
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if ($this->submission_deadline === null) {
            return null;
        }

        return (int) now()->diffInDays($this->submission_deadline, false);
    }

    public function getDeadlineLabelAttribute(): string
    {
        $days = $this->days_until_deadline;

        if ($days === null) {
            return 'Tidak ada deadline';
        }

        if ($days < 0) {
            return 'Overdue ' . abs($days) . ' hari';
        }

        if ($days === 0) {
            return 'Hari ini';
        }

        return 'H-' . $days;
    }
}
