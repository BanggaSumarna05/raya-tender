<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'proposal_id',
        'version_number',
        'revision_number',
        'change_notes',
        // Snapshot fields — immutable after creation
        'title',
        'description',
        'bid_value',
        'deadline',
        'valid_until',
        'status',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at'    => 'datetime',
            'deadline'      => 'date',
            'valid_until'   => 'date',
            'bid_value'     => 'decimal:2',
            'version_number'  => 'integer',
            'revision_number' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Human-readable version label:
     *   revision_number = 0  → "Original"
     *   revision_number = 1  → "Revisi 1"
     *   revision_number = 2  → "Revisi 2"
     */
    public function getRevisionLabelAttribute(): string
    {
        return $this->revision_number === 0
            ? 'Original'
            : 'Revisi ' . $this->revision_number;
    }

    /**
     * Full version label, e.g. "V3 — Revisi 2" or "V1 — Original"
     */
    public function getVersionLabelAttribute(): string
    {
        return "V{$this->version_number} — {$this->revision_label}";
    }
}
