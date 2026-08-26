<?php

namespace App\Enums;

enum DeadlineStatus: string
{
    case NORMAL = 'normal';
    case WARNING = 'warning';
    case CRITICAL = 'critical';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match($this) {
            self::NORMAL => 'Normal',
            self::WARNING => 'Warning',
            self::CRITICAL => 'Critical',
            self::OVERDUE => 'Overdue',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::NORMAL => 'badge-success',
            self::WARNING => 'badge-warning',
            self::CRITICAL => 'badge-danger',
            self::OVERDUE => 'badge-dark',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::NORMAL => 'text-success',
            self::WARNING => 'text-warning',
            self::CRITICAL => 'text-danger',
            self::OVERDUE => 'text-dark',
        };
    }

    /**
     * Calculate deadline status based on days remaining.
     */
    public static function calculate(?\DateTime $deadline): ?self
    {
        if ($deadline === null) {
            return null;
        }

        $now = new \DateTime();
        $diff = $now->diff($deadline);
        $days = (int) $diff->format('%r%a'); // negative if overdue

        if ($days < 0) {
            return self::OVERDUE;
        }

        if ($days <= 3) {
            return self::CRITICAL;
        }

        if ($days <= 7) {
            return self::WARNING;
        }

        return self::NORMAL;
    }
}
