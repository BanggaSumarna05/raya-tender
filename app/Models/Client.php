<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'company_type',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'industry',
        'pic_name',
        'pic_position',
        'pic_phone',
        'pic_email',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [];
    }

    // Relationships

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helpers

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTenderCount(): int
    {
        return $this->tenders()->count();
    }

    public function getWinRate(): float
    {
        $won = $this->tenders()->where('status', 'won')->count();
        $lost = $this->tenders()->where('status', 'lost')->count();
        $total = $won + $lost;

        if ($total === 0) {
            return 0;
        }

        return round(($won / $total) * 100, 1);
    }
}
