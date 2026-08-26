<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case DRAFT = 'draft';
    case INTERNAL_REVIEW = 'internal_review';
    case FINAL = 'final';
    case SUBMITTED = 'submitted';
    case REVISION = 'revision';
    case WON = 'won';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::INTERNAL_REVIEW => 'Internal Review',
            self::FINAL => 'Final',
            self::SUBMITTED => 'Submitted',
            self::REVISION => 'Revision',
            self::WON => 'Won',
            self::LOST => 'Lost',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT => 'badge-secondary',
            self::INTERNAL_REVIEW => 'badge-warning',
            self::FINAL => 'badge-primary',
            self::SUBMITTED => 'badge-info',
            self::REVISION => 'badge-warning',
            self::WON => 'badge-success',
            self::LOST => 'badge-danger',
            self::CANCELLED => 'badge-muted',
        };
    }
}
