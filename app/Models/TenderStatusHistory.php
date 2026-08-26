<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tender_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
