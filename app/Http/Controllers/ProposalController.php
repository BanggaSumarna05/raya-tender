<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\Proposal\StoreProposalRequest;
use App\Http\Requests\Proposal\UpdateProposalRequest;
use App\Models\Proposal;
use App\Models\ProposalVersion;
use App\Models\Tender;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\ProposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProposalController extends Controller
{
    use ValidatesPerPage;

    public function __construct(
        private readonly ProposalService    $proposalService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Proposal::class);

        // bid_value removed from sort — financial field
        $sortField = in_array($request->sort, ['created_at', 'title', 'deadline', 'status'])
            ? $request->sort : 'created_at';
        $sortDir = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = Proposal::with(['tender.client', 'pic', 'versions'])
            ->when($request->search,    fn($q) => $q->search($request->search))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->when($request->tender_id, fn($q) => $q->where('tender_id', $request->tender_id))
            ->when($request->pic_id,    fn($q) => $q->where('pic_id', $request->pic_id))
            // "My Proposal" — only proposals where auth user is PIC
            ->when($request->boolean('my_proposal'), fn($q) => $q->where('pic_id', auth()->id()))
            ->orderBy($sortField, $sortDir);

        $tenders   = Tender::orderBy('title')->get(['id', 'title', 'code']);
        $pics      = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $proposals = $query->paginate($this->getPerPage($request, 10))->withQueryString();

        return view('proposals.index', compact('proposals', 'tenders', 'pics', 'sortField', 'sortDir'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Proposal::class);

        $tenders = Tender::with('client')
            ->whereNotIn('status', ['won', 'lost', 'completed', 'cancelled'])
            ->orderBy('title')
            ->get(['id', 'title', 'code', 'client_id']);
        $pics           = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $selectedTender = $request->tender_id ? Tender::find($request->tender_id) : null;

        return view('proposals.create', compact('tenders', 'pics', 'selectedTender'));
    }

    public function store(StoreProposalRequest $request)
    {
        try {
            $proposal = $this->proposalService->create($request->validated());

            return redirect()
                ->route('proposals.show', $proposal)
                ->with('success', "Proposal {$proposal->code} berhasil dibuat.");
        } catch (\Throwable $e) {
            Log::error('Failed to create proposal', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function show(Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        $proposal->load([
            'tender.client', 'pic', 'backupPic', 'creator', 'updater',
            'documents.category', 'documents.uploader',
            'versions.creator',
        ]);

        return view('proposals.show', compact('proposal'));
    }

    public function edit(Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $tenders = Tender::orderBy('title')->get(['id', 'title', 'code']);
        $pics    = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('proposals.edit', compact('proposal', 'tenders', 'pics'));
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal)
    {
        try {
            $newVersion = $request->boolean('new_version');
            $this->proposalService->update($proposal, $request->validated(), $newVersion);

            return redirect()
                ->route('proposals.show', $proposal)
                ->with('success', 'Proposal berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Failed to update proposal', [
                'proposal_id' => $proposal->id,
                'error'       => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function destroy(Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        try {
            $this->proposalService->delete($proposal);

            return redirect()
                ->route('proposals.index')
                ->with('success', 'Proposal berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete proposal', [
                'proposal_id' => $proposal->id,
                'error'       => $e->getMessage(),
            ]);
            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    /**
     * Create a new revision for a proposal.
     * POST /proposals/{proposal}/revisions
     */
    public function createRevision(Request $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        if (! auth()->user()->can('create_proposal_revisions')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk membuat revisi proposal.');
        }

        $request->validate([
            'revision_note' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $this->proposalService->createRevision($proposal, $request->revision_note);

            return back()->with('success', "Revisi V{$proposal->current_version} berhasil dibuat. Proposal siap untuk diedit.");
        } catch (\Throwable $e) {
            Log::error('Failed to create proposal revision', [
                'proposal_id' => $proposal->id,
                'error'       => $e->getMessage(),
            ]);
            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    /**
     * Show a specific version of a proposal (read-only).
     * GET /proposals/{proposal}/versions/{version}
     */
    public function showVersion(Proposal $proposal, ProposalVersion $version)
    {
        $this->authorize('view', $proposal);

        // Ensure version belongs to this proposal
        abort_if($version->proposal_id !== $proposal->id, 404);

        $proposal->load(['tender.client', 'pic', 'creator']);
        $version->load('creator');

        $isCurrentVersion = ($version->version_number === $proposal->current_version);

        return view('proposals.version', compact('proposal', 'version', 'isCurrentVersion'));
    }

    /**
     * Restore a soft-deleted proposal (Super Admin only).
     */
    public function restore(int $id)
    {
        $proposal = Proposal::withTrashed()->findOrFail($id);
        $proposal->restore();

        $this->activityLogService->log(
            'RESTORE_PROPOSAL', 'Proposal', $proposal->id, Proposal::class,
            "Memulihkan proposal: {$proposal->title}",
            ['code' => $proposal->code]
        );

        return back()->with('success', "Proposal {$proposal->code} berhasil dipulihkan.");
    }

    /**
     * Permanently delete a soft-deleted proposal (Super Admin only).
     */
    public function forceDelete(int $id)
    {
        $proposal = Proposal::withTrashed()->findOrFail($id);

        $this->activityLogService->log(
            'PERMANENT_DELETE_PROPOSAL', 'Proposal', $proposal->id, Proposal::class,
            "Hapus permanen proposal: {$proposal->title}",
            ['code' => $proposal->code]
        );

        $proposal->forceDelete();

        return back()->with('success', "Proposal {$proposal->code} telah dihapus permanen.");
    }

    /**
     * Duplicate a proposal — creates a new draft proposal (new code, fresh version history).
     * NOT the same as creating a revision.
     */
    public function duplicate(Proposal $proposal)
    {
        $this->authorize('create', Proposal::class);

        try {
            $newProposal = $this->proposalService->duplicate($proposal);

            return redirect()
                ->route('proposals.show', $newProposal)
                ->with('success', "Proposal berhasil diduplikasi sebagai {$newProposal->code}. Silakan lengkapi detail.");
        } catch (\Throwable $e) {
            Log::error('Failed to duplicate proposal', ['proposal_id' => $proposal->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menduplikasi proposal.');
        }
    }

    /**
     * FINANCIAL DATA ENDPOINT — Proposal
     *
     * Returns proposal bid_value ONLY to authorized users.
     * Called via AJAX; no raw number sent to frontend without auth.
     */
    public function financialData(Proposal $proposal): JsonResponse
    {
        $this->authorize('view', $proposal);

        if (! auth()->user()->can('view_financial_data')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melihat data finansial.',
            ], 403);
        }

        // Log every access
        $this->activityLogService->log(
            'VIEW_FINANCIAL_DATA',
            'Proposal',
            $proposal->id,
            Proposal::class,
            "Melihat data finansial proposal: {$proposal->code}",
            ['proposal_code' => $proposal->code]
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'bid_value' => $proposal->bid_value
                    ? 'Rp ' . number_format($proposal->bid_value, 0, ',', '.')
                    : null,
            ],
        ]);
    }
}
