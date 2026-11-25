<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use App\Models\Carrier;
use App\Models\CarrierRate;

class StatsController extends Controller
{
    public function statusBreakdown(Request $request)
    {
        $counts = $request->user()->orders()
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

        $total = $counts->sum();

        $breakdown = collect(OrderStatus::cases())->mapWithKeys(function (OrderStatus $s) use ($counts, $total) {
            $count = $counts[$s->value] ?? 0;

            return [$s->value => [
                'count'      => $count,
                'percentage' => $total > 0 ? round($count / $total * 100, 1) : 0,
            ]];
        });

        return response()->json([
            'total'     => $total,
            'breakdown' => $breakdown,
        ]);    
    }
   
    public function shippingCosts(Request $request)
    {
        $data = $request->validate([
            'period' => ['nullable', 'in:day,month,year'],
        ]);

        $format = match ($data['period'] ?? 'month') {
            'day'   => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year'  => '%Y',
        };

        $paidStatuses = array_map(
            fn (OrderStatus $s) => $s->value,
            [OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Returned]
        );

        $rows = $request->user()->orders()
            ->whereIn('status', $paidStatuses)
            ->selectRaw('DATE_FORMAT(created_at, ?) as period, SUM(shipping_cost) as total', [$format])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json($rows);
    }
    public function financialSummary(Request $request)
    {
        $row = $request->user()->orders()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'delivered' THEN total_amount END), 0) as revenue,
                COALESCE(SUM(CASE WHEN status IN ('delivered', 'returned') THEN shipping_cost END), 0) as shipping_paid,
                COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 END), 0) as delivered_count,
                COALESCE(SUM(CASE WHEN status = 'returned' THEN 1 END), 0) as returned_count
            ")
            ->first();

        $delivered = (int) $row->delivered_count;
        $returned  = (int) $row->returned_count;
        $closed    = $delivered + $returned;

        return response()->json([
            'revenue'           => round((float) $row->revenue, 2),
            'shipping_paid'     => round((float) $row->shipping_paid, 2),
            'net_cash'          => round((float) $row->revenue - (float) $row->shipping_paid, 2),
            'delivered_count'   => $delivered,
            'returned_count'    => $returned,
            'success_rate'      => $closed > 0 ? round($delivered / $closed * 100, 1) : null,
        ]);
    }
    
}
