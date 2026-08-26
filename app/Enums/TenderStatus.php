<?php

namespace App\Enums;

enum TenderStatus: string
{
    // --- Pra-aktif ---
    case DRAFT         = 'draft';
    case IDENTIFIED    = 'identified';

    // --- Aktif ---
    case QUALIFICATION = 'qualification';
    case PREPARATION   = 'preparation';
    case SUBMITTED     = 'submitted';

    // --- Evaluasi ---
    case EVALUATION    = 'evaluation';
    case CLARIFICATION = 'clarification';
    case NEGOTIATION   = 'negotiation';

    // --- Final ---
    case WON           = 'won';
    case LOST          = 'lost';
    case COMPLETED     = 'completed';
    case CANCELLED     = 'cancelled';

    // -------------------------------------------------------------------------
    // Labels
    // -------------------------------------------------------------------------

    public function label(): string
    {
        return match($this) {
            self::DRAFT         => 'Draft',
            self::IDENTIFIED    => 'Identified',
            self::QUALIFICATION => 'Qualification',
            self::PREPARATION   => 'Preparation',
            self::SUBMITTED     => 'Submitted',
            self::EVALUATION    => 'Evaluation',
            self::CLARIFICATION => 'Clarification',
            self::NEGOTIATION   => 'Negotiation',
            self::WON           => 'Won',
            self::LOST          => 'Lost',
            self::COMPLETED     => 'Completed',
            self::CANCELLED     => 'Cancelled',
        };
    }

    // -------------------------------------------------------------------------
    // Badge classes (mapped to design system in status-badge component)
    // -------------------------------------------------------------------------

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT         => 'badge-secondary',
            self::IDENTIFIED    => 'badge-info',
            self::QUALIFICATION => 'badge-primary',
            self::PREPARATION   => 'badge-warning',
            self::SUBMITTED     => 'badge-dark',
            self::EVALUATION    => 'badge-info',
            self::CLARIFICATION => 'badge-warning',
            self::NEGOTIATION   => 'badge-primary',
            self::WON           => 'badge-success',
            self::LOST          => 'badge-danger',
            self::COMPLETED     => 'badge-success',
            self::CANCELLED     => 'badge-muted',
        };
    }

    // -------------------------------------------------------------------------
    // Chart / dashboard colors
    // -------------------------------------------------------------------------

    public function color(): string
    {
        return match($this) {
            self::DRAFT         => '#94A3B8',
            self::IDENTIFIED    => '#0BA5EC',
            self::QUALIFICATION => '#1D4ED8',
            self::PREPARATION   => '#D97706',
            self::SUBMITTED     => '#344054',
            self::EVALUATION    => '#0284C7',
            self::CLARIFICATION => '#B45309',
            self::NEGOTIATION   => '#6D28D9',
            self::WON           => '#16A34A',
            self::LOST          => '#DC2626',
            self::COMPLETED     => '#059669',
            self::CANCELLED     => '#9CA3AF',
        };
    }

    // -------------------------------------------------------------------------
    // Status groups
    // -------------------------------------------------------------------------

    /** Statuses that are still "in progress" (not yet closed) */
    public static function activeStatuses(): array
    {
        return [
            self::IDENTIFIED,
            self::QUALIFICATION,
            self::PREPARATION,
            self::SUBMITTED,
            self::EVALUATION,
            self::CLARIFICATION,
            self::NEGOTIATION,
        ];
    }

    public static function closedStatuses(): array
    {
        return [self::WON, self::LOST, self::COMPLETED, self::CANCELLED];
    }

    /** All non-cancelled, non-final statuses (pipeline view) */
    public static function pipelineStatuses(): array
    {
        return [
            self::DRAFT,
            self::IDENTIFIED,
            self::QUALIFICATION,
            self::PREPARATION,
            self::SUBMITTED,
            self::EVALUATION,
            self::CLARIFICATION,
            self::NEGOTIATION,
            self::WON,
            self::LOST,
        ];
    }

    // -------------------------------------------------------------------------
    // Status Transition Map
    // Key   = current status
    // Value = array of allowed NEXT statuses
    // -------------------------------------------------------------------------

    public static function allowedTransitions(): array
    {
        return [
            'draft'         => ['identified', 'cancelled'],
            'identified'    => ['qualification', 'preparation', 'cancelled'],
            'qualification' => ['preparation', 'cancelled'],
            'preparation'   => ['submitted', 'cancelled'],
            'submitted'     => ['evaluation', 'cancelled'],
            'evaluation'    => ['clarification', 'negotiation', 'won', 'lost'],
            'clarification' => ['evaluation', 'negotiation', 'won', 'lost'],
            'negotiation'   => ['won', 'lost'],
            'won'           => ['completed'],
            'lost'          => ['completed'],
            'completed'     => [],   // final
            'cancelled'     => [],   // final
        ];
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = static::allowedTransitions()[$this->value] ?? [];
        return in_array($newStatus, $allowed, true);
    }

    // -------------------------------------------------------------------------
    // Stepper order (used by timeline UI — index = step position)
    // -------------------------------------------------------------------------

    public static function stepperOrder(): array
    {
        return [
            'draft',
            'identified',
            'qualification',
            'preparation',
            'submitted',
            'evaluation',
            'clarification',
            'negotiation',
            'won',       // or 'lost' — both share position 8
        ];
    }

    public function stepIndex(): int
    {
        $order = array_flip(static::stepperOrder());
        return match($this) {
            self::WON, self::LOST, self::COMPLETED => 8,
            self::CANCELLED                         => -1,
            default                                 => $order[$this->value] ?? 0,
        };
    }

    // -------------------------------------------------------------------------
    // Group label for UI grouping
    // -------------------------------------------------------------------------

    public function groupLabel(): string
    {
        return match($this) {
            self::DRAFT                             => 'Draft',
            self::IDENTIFIED, self::QUALIFICATION,
            self::PREPARATION                       => 'Active',
            self::SUBMITTED                         => 'Submitted',
            self::EVALUATION, self::CLARIFICATION,
            self::NEGOTIATION                       => 'Evaluation',
            self::WON, self::LOST, self::COMPLETED  => 'Completed',
            self::CANCELLED                         => 'Cancelled',
        };
    }
}
