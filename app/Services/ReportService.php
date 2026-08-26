<?php

namespace App\Services;

use App\Enums\TenderStatus;
use App\Models\Proposal;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // New workflow active statuses
    private const ACTIVE_STATUSES = [
        'identified', 'qualification', 'preparation',
        'submitted', 'evaluation', 'clarification', 'negotiation',
    ];

    public function getTenderReport(array $filters): array
    {
        $query = Tender::with(['client', 'category', 'pic'])
            ->when(isset($filters['start_date']), fn($q) => $q->whereDate('created_at', '>=', $filters['start_date']))
            ->when(isset($filters['end_date']),   fn($q) => $q->whereDate('created_at', '<=', $filters['end_date']))
            ->when(isset($filters['status']),     fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['client_id']),  fn($q) => $q->where('client_id', $filters['client_id']))
            ->when(isset($filters['category_id']),fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(isset($filters['pic_id']),      fn($q) => $q->where('pic_id', $filters['pic_id']));

        // Do NOT select estimated_value in collection — financial data excluded
        $tenders     = $query->get();
        $won         = $tenders->where('status.value', 'won')->count();
        $lost        = $tenders->where('status.value', 'lost')->count();
        $winRateBase = $won + $lost;

        return [
            'tenders'   => $tenders,
            'total'     => $tenders->count(),
            'won'       => $won,
            'lost'      => $lost,
            'cancelled' => $tenders->where('status.value', 'cancelled')->count(),
            'completed' => $tenders->where('status.value', 'completed')->count(),
            'draft'     => $tenders->where('status.value', 'draft')->count(),
            'active'    => $tenders->whereIn('status.value', self::ACTIVE_STATUSES)->count(),
            'win_rate'  => $winRateBase > 0 ? round(($won / $winRateBase) * 100, 1) : 0,
            // Financial totals intentionally omitted from report listing
        ];
    }

    public function getProposalReport(array $filters): array
    {
        $query = Proposal::with(['tender.client', 'pic'])
            ->when(isset($filters['start_date']), fn($q) => $q->whereDate('created_at', '>=', $filters['start_date']))
            ->when(isset($filters['end_date']),   fn($q) => $q->whereDate('created_at', '<=', $filters['end_date']))
            ->when(isset($filters['status']),     fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['pic_id']),     fn($q) => $q->where('pic_id', $filters['pic_id']));

        $proposals = $query->get();

        return [
            'proposals'       => $proposals,
            'total'           => $proposals->count(),
            'draft'           => $proposals->where('status.value', 'draft')->count(),
            'internal_review' => $proposals->where('status.value', 'internal_review')->count(),
            'final'           => $proposals->where('status.value', 'final')->count(),
            'submitted'       => $proposals->where('status.value', 'submitted')->count(),
            // bid_value totals intentionally omitted — financial data
        ];
    }

    public function getPicPerformance(array $filters): Collection
    {
        return User::select('users.id', 'users.name')
            ->join('tenders', 'users.id', '=', 'tenders.pic_id')
            ->selectRaw('COUNT(tenders.id) as total_tenders')
            ->selectRaw("SUM(CASE WHEN tenders.status = 'won' THEN 1 ELSE 0 END) as won")
            ->selectRaw("SUM(CASE WHEN tenders.status = 'lost' THEN 1 ELSE 0 END) as lost")
            ->groupBy('users.id', 'users.name')
            ->when(isset($filters['start_date']), fn($q) => $q->whereDate('tenders.created_at', '>=', $filters['start_date']))
            ->when(isset($filters['end_date']),   fn($q) => $q->whereDate('tenders.created_at', '<=', $filters['end_date']))
            ->orderByDesc('total_tenders')
            ->get()
            ->map(function ($row) {
                $base          = $row->won + $row->lost;
                $row->win_rate = $base > 0 ? round(($row->won / $base) * 100, 1) : 0;
                return $row;
            });
    }

    public function getClientPerformance(array $filters): Collection
    {
        return DB::table('clients')
            ->join('tenders', 'clients.id', '=', 'tenders.client_id')
            ->select('clients.id', 'clients.name', 'clients.code')
            ->selectRaw('COUNT(tenders.id) as total_tenders')
            ->selectRaw("SUM(CASE WHEN tenders.status = 'won' THEN 1 ELSE 0 END) as won")
            ->selectRaw("SUM(CASE WHEN tenders.status = 'lost' THEN 1 ELSE 0 END) as lost")
            ->groupBy('clients.id', 'clients.name', 'clients.code')
            ->orderByDesc('total_tenders')
            ->get()
            ->map(function ($row) {
                $base          = $row->won + $row->lost;
                $row->win_rate = $base > 0 ? round(($row->won / $base) * 100, 1) : 0;
                return $row;
            });
    }

    public function getDashboardStats(?int $userId = null): array
    {
        // Aggregate by status — no financial values selected here
        $tendersByStatus = Tender::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $proposals = Proposal::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $won         = (int) ($tendersByStatus->get('won')?->total ?? 0);
        $lost        = (int) ($tendersByStatus->get('lost')?->total ?? 0);
        $winRateBase = $won + $lost;

        // Pipeline — count per stage for dashboard
        $pipeline = [];
        foreach (TenderStatus::cases() as $status) {
            $pipeline[$status->value] = (int) ($tendersByStatus->get($status->value)?->total ?? 0);
        }

        $activeTenders = 0;
        foreach (self::ACTIVE_STATUSES as $s) {
            $activeTenders += $pipeline[$s] ?? 0;
        }

        // User-scoped stats (My Work section on dashboard)
        $myStats = [];
        if ($userId) {
            $myStats = [
                'my_tenders'   => Tender::where('pic_id', $userId)
                                        ->whereNotIn('status', ['completed', 'cancelled'])
                                        ->count(),
                'my_proposals' => Proposal::where('pic_id', $userId)
                                          ->whereNotIn('status', ['won', 'lost', 'cancelled'])
                                          ->count(),
                'my_upcoming'  => Tender::where('pic_id', $userId)
                                        ->whereNotIn('status', ['completed', 'cancelled'])
                                        ->whereNotNull('submission_deadline')
                                        ->where('submission_deadline', '>=', now())
                                        ->where('submission_deadline', '<=', now()->addDays(30))
                                        ->with(['client'])
                                        ->orderBy('submission_deadline')
                                        ->limit(5)
                                        ->get(),
                'my_overdue'   => Tender::where('pic_id', $userId)
                                        ->whereNotIn('status', ['completed', 'cancelled'])
                                        ->whereNotNull('submission_deadline')
                                        ->where('submission_deadline', '<', now())
                                        ->count(),
            ];
        }

        return array_merge([
            'total_tenders'     => Tender::count(),
            'active_tenders'    => $activeTenders,
            'won_tenders'       => $won,
            'lost_tenders'      => $lost,
            'win_rate'          => $winRateBase > 0 ? round(($won / $winRateBase) * 100, 1) : 0,
            'active_proposals'  => Proposal::whereIn('status', ['draft', 'internal_review', 'final'])->count(),
            'upcoming_deadlines'=> Tender::upcomingDeadline(30)
                                         ->with(['client', 'pic'])
                                         ->orderBy('submission_deadline')
                                         ->limit(10)
                                         ->get(),
            'overdue_tenders'   => Tender::whereNotIn('status', ['completed', 'cancelled'])
                                         ->whereNotNull('submission_deadline')
                                         ->where('submission_deadline', '<', now())
                                         ->count(),
            'tender_by_status'  => $tendersByStatus,
            'proposal_by_status'=> $proposals,
            'monthly_tenders'   => $this->getMonthlyTenderData(),
            'pipeline'          => $pipeline,
        ], $myStats);
    }

    private function getMonthlyTenderData(): array
    {
        $data = DB::table('tenders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $month          = now()->subMonths($i)->format('Y-m');
            $result[$month] = $data[$month] ?? 0;
        }

        return $result;
    }
}
