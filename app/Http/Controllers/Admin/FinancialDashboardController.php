<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;
use App\Exports\FinancialExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FinancialDashboardController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $today = $this->financialService->getTodayProfit();
        $month = $this->financialService->getMonthProfit();
        $expected = $this->financialService->getExpectedAnalytics();
        $breakdown = $this->financialService->getStatusBreakdown();

        return Inertia::render('Admin/Financial/Dashboard', [
            'stats' => [
                'today' => $today,
                'month' => $month,
                'expected' => $expected,
                'breakdown' => $breakdown
            ]
        ]);
    }

    public function export(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $startDate = null;
        $endDate = Carbon::now();

        switch ($filter) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'custom':
                $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
                $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now();
                break;
        }

        return Excel::download(new FinancialExport($startDate, $endDate), 'financial_report_' . $filter . '.xlsx');
    }
}
