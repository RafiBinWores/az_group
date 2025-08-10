<?php

namespace App\Http\Controllers;

use App\Models\Cutting;
use App\Models\Embroidery;
use App\Models\Finishing;
use App\Models\Order;
use App\Models\PrintReport;
use App\Models\Production;
use App\Models\User;
use App\Models\Wash;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        // Cutting: json column = 'cutting', item key = 'cutting_qty'
        $cutting = $this->twoDayTotals(
            modelClass: Cutting::class,
            dateColumn: 'date',
            jsonColumn: 'cutting',
            itemSum: fn(array $item) => (int)($item['cutting_qty'] ?? 0)
        );

        // Embroidery: json column = 'embroidery_data', sum by 'received'
        // (If your table combines embroidery & print, point to that model/column)
        $embroidery = $this->twoDayTotals(
            modelClass: Embroidery::class,
            dateColumn: 'date',
            jsonColumn: 'embroidery_data',   // change to your actual column
            itemSum: fn(array $item) => (int)($item['received'] ?? 0)
        );

        // Print: json column = 'print_data', sum by 'received'
        $print = $this->twoDayTotals(
            modelClass: PrintReport::class,
            dateColumn: 'date',
            jsonColumn: 'print_data',
            itemSum: fn(array $item) => (int)($item['received'] ?? 0)
        );

        // Wash: json column = 'wash_data', sum by 'received'
        $wash = $this->twoDayTotals(
            modelClass: Wash::class,
            dateColumn: 'date',
            jsonColumn: 'wash_data',
            itemSum: fn(array $item) => (int)($item['received'] ?? 0)
        );

        // Production: json column = 'production_data', sum by 'total_output'
        $production = $this->twoDayTotals(
            modelClass: Production::class,
            dateColumn: 'date',
            jsonColumn: 'production_data',
            itemSum: fn(array $item) => (int)($item['total_output'] ?? 0)
        );

        // Group orders by month/year and sum order_qty
        $ordersByMonth = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(order_qty) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(fn($r) => [
                'label' => \Carbon\Carbon::createFromFormat('Y-m', $r->ym)->format('M Y'),
                'total' => (int) $r->total,
            ]);

        // Latest day with finishing data
        $finLatestDay = Finishing::whereNotNull('date')->max('date');

        $finTotalLatest = $finLatestDay
            ? Finishing::whereDate('date', $finLatestDay)->sum('today_finishing') // or 'total_finishing'
            : 0;

        // Previous day that has data (strictly before latest)
        $finPrevDay = $finLatestDay
            ? Finishing::whereDate('date', '<', $finLatestDay)->max('date')
            : null;

        $finTotalPrev = $finPrevDay
            ? Finishing::whereDate('date', $finPrevDay)->sum('today_finishing') // or 'total_finishing'
            : 0;

        // Change % and direction
        $diff = $finTotalLatest - $finTotalPrev;
        $finDirection = $diff >= 0 ? 'up' : 'down';
        $finChange = $finTotalPrev > 0
            ? round(($diff / $finTotalPrev) * 100)
            : ($finTotalLatest > 0 ? 100 : 0);

        // Small date formatter for the blade
        $formatDate = fn($d) => $d ? Carbon::parse($d)->format('M d, Y') : 'N/A';

       $orders = Order::latest()->limit(5)->get();
       $users = User::latest()->limit(5)->get();


        return view('dashboard', [
            // Cutting
            'cuttingLatestDay'   => $cutting['latestDay'],
            'cuttingTotalLatest' => $cutting['totalLatest'],
            'cuttingPrevDay'     => $cutting['previousDay'],
            'cuttingTotalPrev'   => $cutting['totalPrevious'],
            'cuttingChange'      => $cutting['percentChange'],
            'cuttingDirection'   => $cutting['direction'],

            // Embroidery
            'embLatestDay'       => $embroidery['latestDay'],
            'embTotalLatest'     => $embroidery['totalLatest'],
            'embPrevDay'         => $embroidery['previousDay'],
            'embTotalPrev'       => $embroidery['totalPrevious'],
            'embChange'          => $embroidery['percentChange'],
            'embDirection'       => $embroidery['direction'],

            // Print
            'printLatestDay'     => $print['latestDay'],
            'printTotalLatest'   => $print['totalLatest'],
            'printPrevDay'       => $print['previousDay'],
            'printTotalPrev'     => $print['totalPrevious'],
            'printChange'        => $print['percentChange'],
            'printDirection'     => $print['direction'],

            // Wash
            'washLatestDay'      => $wash['latestDay'],
            'washTotalLatest'    => $wash['totalLatest'],
            'washPrevDay'        => $wash['previousDay'],
            'washTotalPrev'      => $wash['totalPrevious'],
            'washChange'         => $wash['percentChange'],
            'washDirection'      => $wash['direction'],

            // Production
            'prodLatestDay'      => $production['latestDay'],
            'prodTotalLatest'    => $production['totalLatest'],
            'prodPrevDay'        => $production['previousDay'],
            'prodTotalPrev'      => $production['totalPrevious'],
            'prodChange'         => $production['percentChange'],
            'prodDirection'      => $production['direction'],

            'ordersByMonth' => $ordersByMonth,

            'finLatestDay' => $finLatestDay,
            'finPrevDay' => $finPrevDay,
            'finTotalLatest' => $finTotalLatest,
            'finDirection' => $finDirection,
            'finChange' => $finChange,
            'formatDate' => $formatDate,

            'orders' => $orders,
            'users' => $users,
        ]);
    }

    /**
     * Generic “two most recent report days” metric builder for JSON-row tables.
     *
     * @param class-string $modelClass
     * @param string $dateColumn
     * @param string $jsonColumn
     * @param callable(array $item): int $itemSum  // returns numeric amount per JSON item
     * @return array{latestDay: mixed, previousDay: mixed, totalLatest:int, totalPrevious:int, percentChange: float|int, direction: 'up'|'down'}
     */
    private function twoDayTotals(string $modelClass, string $dateColumn, string $jsonColumn, callable $itemSum): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelClass */

        // 1) Two most recent unique dates
        /** @var Collection $recentDays */
        $recentDays = $modelClass::query()
            ->orderByDesc($dateColumn)
            ->pluck($dateColumn)
            ->unique()
            ->take(2)
            ->values();

        $latestDay   = $recentDays->get(0);
        $previousDay = $recentDays->get(1);

        // 2) Rows for those dates
        $latestRows = $latestDay
            ? $modelClass::whereDate($dateColumn, $latestDay)->get()
            : collect();
        $previousRows = $previousDay
            ? $modelClass::whereDate($dateColumn, $previousDay)->get()
            : collect();

        // 3) Sum totals using the provided JSON column + itemSum callback
        $sumRows = function (Collection $rows) use ($jsonColumn, $itemSum): int {
            return (int) $rows->sum(function ($row) use ($jsonColumn, $itemSum) {
                $data = $row->{$jsonColumn};
                if (is_string($data)) {
                    $data = json_decode($data, true);
                }
                if (!is_array($data)) return 0;

                $total = 0;
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $total += (int) $itemSum($item);
                    }
                }
                return $total;
            });
        };

        $totalLatest   = $sumRows($latestRows);
        $totalPrevious = $sumRows($previousRows);

        // 4) Percent change + direction
        $percentChange = $totalPrevious > 0
            ? round((($totalLatest - $totalPrevious) / $totalPrevious) * 100, 2)
            : 100;
        $direction = $totalLatest >= $totalPrevious ? 'up' : 'down';

        return [
            'latestDay'     => $latestDay,
            'previousDay'   => $previousDay,
            'totalLatest'   => $totalLatest,
            'totalPrevious' => $totalPrevious,
            'percentChange' => $percentChange,
            'direction'     => $direction,
        ];
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
