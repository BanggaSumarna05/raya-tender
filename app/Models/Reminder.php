<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'proposal_id',
        'user_id',
        'title',
        'description',
        'reminder_at',
        'type',
        'status',
        'sent_at',
        'event_key',
    ];

    protected function casts(): array
    {
        return [
            'reminder_at' => 'datetime',
            'sent_at'     => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
