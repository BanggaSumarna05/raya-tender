<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes — hanya untuk guest
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes — wajib login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Root redirect
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications (AJAX)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Global Search (AJAX + page)
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    // Ganti Password
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->middleware('throttle:5,1')
        ->name('password.change.update');

    // ---------------------------------------------------------------
    // Tenders
    // ---------------------------------------------------------------
    Route::resource('tenders', TenderController::class);

    // Status change — requires change_tender_status permission
    Route::post('tenders/{tender}/change-status', [TenderController::class, 'changeStatus'])
        ->middleware('permission:change_tender_status')
        ->name('tenders.change-status');

    // Duplicate tender
    Route::post('tenders/{tender}/duplicate', [TenderController::class, 'duplicate'])
        ->middleware('permission:create_tenders')
        ->name('tenders.duplicate');

    // Financial data — AJAX endpoint, requires view_financial_data permission
    // Note: permission check is done inside the controller for JSON 403 response
    Route::get('tenders/{tender}/financial-data', [TenderController::class, 'financialData'])
        ->name('tenders.financial-data');

    // ---------------------------------------------------------------
    // Proposals
    // ---------------------------------------------------------------
    Route::resource('proposals', ProposalController::class);

    // Revision — creates a new version snapshot
    Route::post('proposals/{proposal}/revisions', [ProposalController::class, 'createRevision'])
        ->middleware('permission:create_proposal_revisions')
        ->name('proposals.revisions.create');

    // Duplicate proposal
    Route::post('proposals/{proposal}/duplicate', [ProposalController::class, 'duplicate'])
        ->middleware('permission:create_proposals')
        ->name('proposals.duplicate');

    // Version detail — read-only view of a specific version
    Route::get('proposals/{proposal}/versions/{version}', [ProposalController::class, 'showVersion'])
        ->name('proposals.versions.show');

    // Financial data — AJAX endpoint
    Route::get('proposals/{proposal}/financial-data', [ProposalController::class, 'financialData'])
        ->name('proposals.financial-data');

    // ---------------------------------------------------------------
    // Documents
    // ---------------------------------------------------------------
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->middleware('throttle:30,1')
        ->name('documents.download');

    // ---------------------------------------------------------------
    // Clients
    // ---------------------------------------------------------------
    Route::resource('clients', ClientController::class);

    // ---------------------------------------------------------------
    // Reports
    // ---------------------------------------------------------------
    Route::prefix('reports')->name('reports.')->group(function () {

        Route::middleware('permission:view_reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/tenders', [ReportController::class, 'tenderReport'])->name('tenders');
            Route::get('/proposals', [ReportController::class, 'proposalReport'])->name('proposals');
            Route::get('/performance', [ReportController::class, 'performanceReport'])->name('performance');
        });

        Route::middleware(['permission:export_reports', 'throttle:10,1'])->group(function () {
            // Column selector — simpan pilihan kolom ke session sebelum export
            Route::post('/export/tender-columns', [ReportController::class, 'saveTenderExportColumns'])
                ->name('export.tender-columns')
                ->withoutMiddleware('throttle:10,1');

            Route::get('/export/tender-excel', [ReportController::class, 'exportTenderExcel'])
                ->name('export.tender-excel');
            Route::get('/export/tender-pdf', [ReportController::class, 'exportTenderPdf'])
                ->name('export.tender-pdf');
            Route::get('/export/proposal-excel', [ReportController::class, 'exportProposalExcel'])
                ->name('export.proposal-excel');
            Route::get('/export/proposal-pdf', [ReportController::class, 'exportProposalPdf'])
                ->name('export.proposal-pdf');
            Route::get('/export/tender-raw', [ReportController::class, 'exportTenderRaw'])
                ->name('export.tender-raw');
            Route::get('/export/proposal-raw', [ReportController::class, 'exportProposalRaw'])
                ->name('export.proposal-raw');
            Route::get('/export/summary-pdf', [ReportController::class, 'exportSummaryPdf'])
                ->name('export.summary-pdf');
        });
    });

    // ---------------------------------------------------------------
    // Trash & Restore — Super Admin only
    // ---------------------------------------------------------------
    Route::middleware('role:Super Admin')->prefix('trash')->name('trash.')->group(function () {
        // Tenders
        Route::post('tenders/{id}/restore',  [TenderController::class,   'restore'])->name('tenders.restore');
        Route::delete('tenders/{id}',        [TenderController::class,   'forceDelete'])->name('tenders.force-delete');
        // Proposals
        Route::post('proposals/{id}/restore', [ProposalController::class, 'restore'])->name('proposals.restore');
        Route::delete('proposals/{id}',       [ProposalController::class, 'forceDelete'])->name('proposals.force-delete');
        // Clients
        Route::post('clients/{id}/restore',   [ClientController::class,   'restore'])->name('clients.restore');
        Route::delete('clients/{id}',         [ClientController::class,   'forceDelete'])->name('clients.force-delete');
    });

    // ---------------------------------------------------------------
    // Settings
    // ---------------------------------------------------------------
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::middleware('permission:view_categories')->group(function () {
            Route::resource('categories', CategoryController::class)->except(['show']);
        });

        Route::middleware('permission:view_users')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');
        });

        Route::get('activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:view_activity_logs')
            ->name('activity-logs.index');
    });
});
