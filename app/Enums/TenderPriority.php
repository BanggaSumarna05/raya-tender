<?php

namespace App\Enums;

enum TenderPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::LOW => 'badge-muted',
            self::MEDIUM => 'badge-info',
            self::HIGH => 'badge-warning',
            self::URGENT => 'badge-danger',
        };
    }
}
