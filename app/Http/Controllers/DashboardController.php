<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $statusSummary = FlightBooking::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->select('status', \DB::raw('COUNT(*) as count'))
                            ->groupBy('status')
                            ->get()
                            ->keyBy('status');

        // dd($statusSummary );
        return view('dashboard.index', compact('statusSummary'));
    }
}
