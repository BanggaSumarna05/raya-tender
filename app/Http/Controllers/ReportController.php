<?php

namespace App\Http\Controllers;

use App\Exports\ProposalReportExport;
use App\Exports\ProposalRawExport;
use App\Exports\TenderReportExport;
use App\Exports\TenderRawExport;
use App\Models\Client;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index()
    {
        abort_unless(auth()->user()->can('view_reports'), 403);

        return view('reports.index');
    }

    public function tenderReport(Request $request)
    {
        abort_unless(auth()->user()->can('view_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);
        $data    = $this->reportService->getTenderReport($filters);

        $clients = Client::active()->orderBy('name')->get(['id', 'name']);
        $pics    = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('reports.tender', compact('data', 'filters', 'clients', 'pics'));
    }

    public function proposalReport(Request $request)
    {
        abort_unless(auth()->user()->can('view_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'pic_id']);
        $data    = $this->reportService->getProposalReport($filters);

        $pics = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('reports.proposal', compact('data', 'filters', 'pics'));
    }

    public function performanceReport(Request $request)
    {
        abort_unless(auth()->user()->can('view_reports'), 403);

        $filters    = $request->only(['start_date', 'end_date']);
        $picData    = $this->reportService->getPicPerformance($filters);
        $clientData = $this->reportService->getClientPerformance($filters);

        return view('reports.performance', compact('picData', 'clientData', 'filters'));
    }

    // ── Tender Exports ────────────────────────────────────────────────────────

    /**
     * Daftar semua kolom yang tersedia untuk export tender.
     * Key = nama kolom internal, value = label tampilan.
     */
    public static function availableTenderColumns(): array
    {
        return [
            'code'                 => 'Kode Tender',
            'title'                => 'Nama Tender',
            'client'               => 'Klien',
            'category'             => 'Kategori',
            'status'               => 'Status',
            'priority'             => 'Prioritas',
            'pic'                  => 'PIC',
            'backup_pic'           => 'Backup PIC',
            'location'             => 'Lokasi',
            'source'               => 'Sumber',
            'received_date'        => 'Tgl. Diterima',
            'submission_deadline'  => 'Deadline Submission',
            'project_start_date'   => 'Tgl. Mulai Proyek',
            'project_end_date'     => 'Tgl. Selesai Proyek',
            'estimated_value'      => 'Nilai Estimasi',   // finansial — dibatasi permission
            'description'          => 'Deskripsi',
            'notes'                => 'Catatan',
            'created_at'           => 'Tgl. Dibuat',
        ];
    }

    /**
     * Kolom default jika user belum memilih.
     */
    public static function defaultTenderColumns(): array
    {
        return ['code', 'title', 'client', 'category', 'pic', 'status', 'priority', 'submission_deadline', 'location', 'created_at'];
    }

    /**
     * Daftar nilai status tender yang valid untuk filter export.
     */
    public static function validTenderStatuses(): array
    {
        return array_map(fn($s) => $s->value, \App\Enums\TenderStatus::cases());
    }

    /**
     * Simpan pilihan kolom & status ke session, lalu redirect ke endpoint export yang dipilih.
     */
    public function saveTenderExportColumns(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $allowed      = array_keys(self::availableTenderColumns());
        $canFinancial = auth()->user()->can('view_financial_data');

        // Validasi kolom
        $selected = collect($request->input('columns', []))
            ->filter(fn($col) => in_array($col, $allowed, true))
            ->filter(fn($col) => $col !== 'estimated_value' || $canFinancial)
            ->values()
            ->all();

        if (empty($selected)) {
            $selected = self::defaultTenderColumns();
        }

        session(['tender_export_columns' => $selected]);

        // Validasi & simpan export_statuses (hanya jika kolom status diceklis)
        if (in_array('status', $selected, true)) {
            $validStatuses    = self::validTenderStatuses();
            $exportStatuses   = collect($request->input('export_statuses', []))
                ->filter(fn($s) => in_array($s, $validStatuses, true))
                ->values()
                ->all();

            // [] = semua status (All), array berisi = filter spesifik
            session(['tender_export_statuses' => $exportStatuses]);
        } else {
            session()->forget('tender_export_statuses');
        }

        $exportType = $request->input('export_type', 'excel');
        $filters    = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);

        if ($exportType === 'pdf') {
            return redirect()->route('reports.export.tender-pdf', $filters);
        }

        return redirect()->route('reports.export.tender-excel', $filters);
    }

    public function exportTenderExcel(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);

        $columns        = session('tender_export_columns', self::defaultTenderColumns());
        $exportStatuses = session('tender_export_statuses', []);   // [] = semua status
        session()->forget(['tender_export_columns', 'tender_export_statuses']);

        return Excel::download(
            new TenderReportExport($filters, $columns, $exportStatuses),
            'laporan-tender-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportTenderRaw(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);

        return Excel::download(
            new TenderRawExport($filters),
            'data-tender-raw-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportTenderPdf(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);

        $columns        = session('tender_export_columns', self::defaultTenderColumns());
        $exportStatuses = session('tender_export_statuses', []);   // [] = semua status
        $columnLabels   = self::availableTenderColumns();
        session()->forget(['tender_export_columns', 'tender_export_statuses']);

        // Terapkan filter status dari modal jika ada
        if (!empty($exportStatuses)) {
            $filters['statuses'] = $exportStatuses;
        }

        $data = $this->reportService->getTenderReport($filters);

        return Pdf::loadView('reports.exports.tender-pdf', compact('data', 'filters', 'columns', 'columnLabels', 'exportStatuses'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-tender-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Proposal Exports ──────────────────────────────────────────────────────

    public function exportProposalExcel(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'pic_id']);

        return Excel::download(
            new ProposalReportExport($filters),
            'laporan-proposal-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportProposalRaw(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'pic_id']);

        return Excel::download(
            new ProposalRawExport($filters),
            'data-proposal-raw-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportProposalPdf(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'pic_id']);
        $data    = $this->reportService->getProposalReport($filters);

        // Eager load relationships needed by PDF view
        $data['proposals']->load(['tender.client', 'pic']);

        return Pdf::loadView('reports.exports.proposal-pdf', compact('data', 'filters'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-proposal-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Summary Export (Tender + Proposal + Performa) ─────────────────────────

    public function exportSummaryPdf(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date']);

        $tenderData   = $this->reportService->getTenderReport($filters);
        $proposalData = $this->reportService->getProposalReport($filters);

        // Eager load relationships needed by the PDF view
        $tenderData['tenders']->load(['client', 'category', 'pic']);
        $proposalData['proposals']->load(['tender.client', 'pic']);

        return Pdf::loadView('reports.exports.summary-pdf', compact(
            'tenderData',
            'proposalData',
            'filters'
        ))
            ->setPaper('a4', 'landscape')
            ->download('laporan-keseluruhan-' . now()->format('Y-m-d') . '.pdf');
    }
}
