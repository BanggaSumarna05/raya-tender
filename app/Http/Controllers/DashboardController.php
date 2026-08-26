<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index()
    {
        // Pass the authenticated user ID so ReportService can compute My Work stats
        $stats = $this->reportService->getDashboardStats(auth()->id());

        return view('dashboard.index', compact('stats'));
    }
}
