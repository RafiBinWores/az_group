<?php

namespace App\Http\Controllers;

use App\Models\Cutting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        // 1. Get the two most recent unique report dates
        $recentReportDays = Cutting::orderByDesc('date')
            ->pluck('date')
            ->unique()
            ->take(2)
            ->values();

        $latestReportDay = $recentReportDays->get(0);
        $previousReportDay = $recentReportDays->get(1);

        // 2. Get all Cutting records for those dates
        $latestRows = $latestReportDay
            ? Cutting::whereDate('date', $latestReportDay)->get()
            : collect();
        $previousRows = $previousReportDay
            ? Cutting::whereDate('date', $previousReportDay)->get()
            : collect();

        // 3. Calculate total cutting quantity for both dates
        $totalLatest = $latestRows->sum(function ($row) {
            $cuttings = is_string($row->cutting) ? json_decode($row->cutting, true) : $row->cutting;
            return is_array($cuttings) ? collect($cuttings)->sum('cutting_qty') : 0;
        });
        $totalPrevious = $previousRows->sum(function ($row) {
            $cuttings = is_string($row->cutting) ? json_decode($row->cutting, true) : $row->cutting;
            return is_array($cuttings) ? collect($cuttings)->sum('cutting_qty') : 0;
        });

        // 4. Calculate percent change and direction
        $cuttingChange = $totalPrevious > 0
            ? round((($totalLatest - $totalPrevious) / $totalPrevious) * 100, 2)
            : 100;
        $cuttingDirection = $totalLatest >= $totalPrevious ? 'up' : 'down';

        // 5. Pass values to the view
        return view('dashboard', [
            'latestReportDay'      => $latestReportDay,
            'totalLatest'          => $totalLatest,
            'previousReportDay'    => $previousReportDay,
            'totalPrevious'        => $totalPrevious,
            'cuttingChange'        => $cuttingChange,
            'cuttingDirection'     => $cuttingDirection,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
