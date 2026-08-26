<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Proposal;
use App\Models\Tender;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    use ValidatesPerPage;
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $sortField = in_array($request->sort, ['created_at','name','file_size','extension'])
                     ? $request->sort : 'created_at';
        $sortDir   = $request->direction === 'asc' ? 'asc' : 'desc';

        $query = Document::with(['tender', 'proposal', 'category', 'uploader'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('original_name', 'like', "%{$request->search}%"))
            ->when($request->tender_id, fn($q) => $q->where('tender_id', $request->tender_id))
            ->when($request->proposal_id, fn($q) => $q->where('proposal_id', $request->proposal_id))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id));

        $documents  = $query->orderBy($sortField, $sortDir)->paginate($this->getPerPage($request, 15))->withQueryString();
        $tenders    = Tender::orderBy('title')->get(['id', 'title', 'code']);
        $categories = DocumentCategory::active()->orderBy('name')->get(['id', 'name']);

        return view('documents.index', compact('documents', 'tenders', 'categories', 'sortField', 'sortDir'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Document::class);

        $tenders    = Tender::orderBy('title')->get(['id', 'title', 'code']);
        $proposals  = Proposal::with('tender')->orderBy('title')->get(['id', 'title', 'code', 'tender_id']);
        $categories = DocumentCategory::active()->orderBy('name')->get(['id', 'name']);

        $selectedTenderId   = $request->tender_id;
        $selectedProposalId = $request->proposal_id;

        return view('documents.create', compact('tenders', 'proposals', 'categories', 'selectedTenderId', 'selectedProposalId'));
    }

    public function store(StoreDocumentRequest $request)
    {
        try {
            $document = $this->documentService->store($request->validated(), $request->file('file'));

            return redirect()
                ->route('documents.index')
                ->with('success', "Dokumen {$document->name} berhasil diunggah.");
        } catch (\Throwable $e) {
            Log::error('Failed to upload document', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Gagal mengunggah dokumen. Silakan coba kembali.');
        }
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        $document->load(['tender', 'proposal', 'category', 'uploader']);

        return view('documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        $this->authorize('download', $document);

        try {
            return $this->documentService->download($document);
        } catch (\Throwable $e) {
            Log::error('Failed to download document', ['document_id' => $document->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'File tidak ditemukan atau gagal diunduh.');
        }
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        try {
            $this->documentService->delete($document);

            return back()->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete document', ['document_id' => $document->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }
}
