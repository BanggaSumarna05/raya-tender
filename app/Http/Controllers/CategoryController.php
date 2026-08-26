<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\TenderCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ValidatesPerPage;

    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', TenderCategory::class);

        $sortField = in_array($request->sort, ['created_at','name','code','tenders_count'])
                     ? $request->sort : 'created_at';
        $sortDir   = $request->direction === 'asc' ? 'asc' : 'desc';

        $categories = TenderCategory::withCount('tenders')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy($sortField === 'tenders_count' ? 'tenders_count' : $sortField, $sortDir)
            ->paginate($this->getPerPage($request, 15))
            ->withQueryString();

        return view('categories.index', compact('categories', 'sortField', 'sortDir'));
    }

    public function create()
    {
        $this->authorize('create', TenderCategory::class);

        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = TenderCategory::create($request->validated());

            $this->activityLogService->log(
                'CREATE_CATEGORY', 'Category',
                $category->id, TenderCategory::class,
                "Membuat kategori: {$category->name}"
            );

            return redirect()
                ->route('settings.categories.index')
                ->with('success', "Kategori {$category->name} berhasil dibuat.");
        } catch (\Throwable $e) {
            Log::error('Failed to create category', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function edit(TenderCategory $category)
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, TenderCategory $category)
    {
        try {
            $category->update($request->validated());

            $this->activityLogService->log(
                'UPDATE_CATEGORY', 'Category',
                $category->id, TenderCategory::class,
                "Memperbarui kategori: {$category->name}"
            );

            return redirect()
                ->route('settings.categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Failed to update category', ['category_id' => $category->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function destroy(TenderCategory $category)
    {
        $this->authorize('delete', $category);

        try {
            if ($category->tenders()->exists()) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh tender.');
            }

            $this->activityLogService->log(
                'DELETE_CATEGORY', 'Category',
                $category->id, TenderCategory::class,
                "Menghapus kategori: {$category->name}"
            );

            $category->delete();

            return redirect()
                ->route('settings.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete category', ['category_id' => $category->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }
}
