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

    public function exportTenderExcel(Request $request)
    {
        abort_unless(auth()->user()->can('export_reports'), 403);

        $filters = $request->only(['start_date', 'end_date', 'status', 'client_id', 'category_id', 'pic_id']);

        return Excel::download(
            new TenderReportExport($filters),
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
        $data    = $this->reportService->getTenderReport($filters);

        return Pdf::loadView('reports.exports.tender-pdf', compact('data', 'filters'))
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
