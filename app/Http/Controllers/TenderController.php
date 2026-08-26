<?php

namespace App\Http\Controllers;

use App\Enums\TenderStatus;
use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\Tender\StoreTenderRequest;
use App\Http\Requests\Tender\UpdateTenderRequest;
use App\Models\Client;
use App\Models\Tender;
use App\Models\TenderCategory;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\TenderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenderController extends Controller
{
    use ValidatesPerPage;

    public function __construct(
        private readonly TenderService      $tenderService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Tender::class);

        // Only valid new status values in sort whitelist
        $sortField = in_array($request->sort, [
            'created_at', 'title', 'submission_deadline', 'status', 'priority',
        ]) ? $request->sort : 'created_at';
        // estimated_value intentionally removed from sort — financial field
        $sortDir = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = Tender::with(['client', 'category', 'pic', 'backupPic'])
            ->when($request->search,       fn($q) => $q->search($request->search))
            ->when($request->status,       fn($q) => $q->where('status', $request->status))
            ->when($request->client_id,    fn($q) => $q->where('client_id', $request->client_id))
            ->when($request->category_id,  fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->pic_id,       fn($q) => $q->where('pic_id', $request->pic_id))
            ->when($request->priority,     fn($q) => $q->where('priority', $request->priority))
            ->when($request->deadline_from, fn($q) => $q->whereDate('submission_deadline', '>=', $request->deadline_from))
            ->when($request->deadline_to,  fn($q) => $q->whereDate('submission_deadline', '<=', $request->deadline_to))
            // "My Tender" — only tenders where auth user is PIC
            ->when($request->boolean('my_tender'), fn($q) => $q->where('pic_id', auth()->id()))
            ->orderBy($sortField, $sortDir);

        $tenders    = $query->paginate($this->getPerPage($request, 10))->withQueryString();
        $clients    = Client::active()->orderBy('name')->get(['id', 'name']);
        $categories = TenderCategory::active()->orderBy('name')->get(['id', 'name']);
        $pics       = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $statuses   = TenderStatus::cases();

        return view('tenders.index', compact(
            'tenders', 'clients', 'categories', 'pics', 'statuses', 'sortField', 'sortDir'
        ));
    }

    public function create()
    {
        $this->authorize('create', Tender::class);

        $clients    = Client::active()->orderBy('name')->get(['id', 'name']);
        $categories = TenderCategory::active()->orderBy('name')->get(['id', 'name']);
        $pics       = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('tenders.create', compact('clients', 'categories', 'pics'));
    }

    public function store(StoreTenderRequest $request)
    {
        try {
            $tender = $this->tenderService->create($request->validated());

            return redirect()
                ->route('tenders.show', $tender)
                ->with('success', "Tender {$tender->code} berhasil dibuat.");
        } catch (\Throwable $e) {
            Log::error('Failed to create tender', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function show(Tender $tender)
    {
        $this->authorize('view', $tender);

        $tender->load([
            'client', 'category', 'pic', 'backupPic', 'creator', 'updater',
            'proposals.pic',
            'documents.category', 'documents.uploader',
            'statusHistories.changedBy',
        ]);

        // Build allowed transitions for current status (for UI dropdown)
        $allowedTransitions = TenderStatus::allowedTransitions()[$tender->status->value] ?? [];

        return view('tenders.show', compact('tender', 'allowedTransitions'));
    }

    public function edit(Tender $tender)
    {
        $this->authorize('update', $tender);

        $clients    = Client::active()->orderBy('name')->get(['id', 'name']);
        $categories = TenderCategory::active()->orderBy('name')->get(['id', 'name']);
        $pics       = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('tenders.edit', compact('tender', 'clients', 'categories', 'pics'));
    }

    public function update(UpdateTenderRequest $request, Tender $tender)
    {
        try {
            $this->tenderService->update($tender, $request->validated());

            return redirect()
                ->route('tenders.show', $tender)
                ->with('success', 'Tender berhasil diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Failed to update tender', ['tender_id' => $tender->id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function destroy(Tender $tender)
    {
        $this->authorize('delete', $tender);

        try {
            $this->tenderService->delete($tender);

            return redirect()
                ->route('tenders.index')
                ->with('success', 'Tender berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete tender', ['tender_id' => $tender->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    /**
     * Change tender status — validates transition rules.
     */
    public function changeStatus(Request $request, Tender $tender)
    {
        $this->authorize('update', $tender);

        // Build valid transition list from current status
        $allowedNext = TenderStatus::allowedTransitions()[$tender->status->value] ?? [];

        $request->validate([
            'status'   => ['required', 'string', 'in:' . implode(',', $allowedNext)],
            'notes'    => ['nullable', 'string', 'max:2000'],
            'lost_reason' => ['required_if:status,lost', 'nullable', 'string', 'max:2000'],
        ]);

        try {
            // Save auxiliary reason fields before status change
            if ($request->status === 'lost' && $request->lost_reason) {
                $tender->update(['lost_reason' => $request->lost_reason]);
            }

            $this->tenderService->changeStatus($tender, $request->status, $request->notes);

            return back()->with('success', 'Status tender berhasil diubah.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Failed to change tender status', [
                'tender_id' => $tender->id,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    /**
     * Restore a soft-deleted tender (Super Admin only).
     */
    public function restore(int $id)
    {
        $tender = Tender::withTrashed()->findOrFail($id);

        $tender->restore();

        $this->activityLogService->log(
            'RESTORE_TENDER', 'Tender', $tender->id, Tender::class,
            "Memulihkan tender: {$tender->title}",
            ['code' => $tender->code]
        );

        return back()->with('success', "Tender {$tender->code} berhasil dipulihkan.");
    }

    /**
     * Permanently delete a soft-deleted tender (Super Admin only).
     */
    public function forceDelete(int $id)
    {
        $tender = Tender::withTrashed()->findOrFail($id);

        $this->activityLogService->log(
            'PERMANENT_DELETE_TENDER', 'Tender', $tender->id, Tender::class,
            "Hapus permanen tender: {$tender->title}",
            ['code' => $tender->code]
        );

        $tender->forceDelete();

        return back()->with('success', "Tender {$tender->code} telah dihapus permanen.");
    }

    /**
     * Duplicate a tender — creates a new draft tender from an existing one.
     */
    public function duplicate(Tender $tender)
    {
        $this->authorize('create', Tender::class);

        try {
            $newTender = $this->tenderService->duplicate($tender);

            return redirect()
                ->route('tenders.show', $newTender)
                ->with('success', "Tender berhasil diduplikasi sebagai {$newTender->code}. Silakan lengkapi detail.");
        } catch (\Throwable $e) {
            Log::error('Failed to duplicate tender', ['tender_id' => $tender->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menduplikasi tender.');
        }
    }

    /**
     * FINANCIAL DATA ENDPOINT
     *
     * Returns tender financial data ONLY to authorized users.
     * No financial values are included in the standard show() response.
     * This endpoint is called via AJAX when the user clicks "Tampilkan".
     *
     * Security:
     *  - Authentication: handled by 'auth' middleware on the route group
     *  - Authorization: requires 'view_financial_data' permission
     *  - Activity: every successful access is logged
     *  - Response: financial value is formatted server-side, raw number NOT sent
     */
    public function financialData(Tender $tender): JsonResponse
    {
        $this->authorize('view', $tender);

        if (! auth()->user()->can('view_financial_data')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melihat data finansial.',
            ], 403);
        }

        // Log every successful financial data access
        $this->activityLogService->log(
            'VIEW_FINANCIAL_DATA',
            'Tender',
            $tender->id,
            Tender::class,
            "Melihat data finansial tender: {$tender->code}",
            ['tender_code' => $tender->code]
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'estimated_value' => $tender->estimated_value
                    ? 'Rp ' . number_format($tender->estimated_value, 0, ',', '.')
                    : null,
            ],
        ]);
    }
}
