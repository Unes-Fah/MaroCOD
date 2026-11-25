<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderLogisticsService;

class AuditPendingOrders extends Command
{
    protected $signature = 'orders:audit-pending {--hours=48 : Expire orders pending longer than this many hours}
    {--dry-run : Counts and reports the orders that should expire}
    ';

    protected $description = 'Expire orders that have been pending for too long';

    public function handle(OrderLogisticsService $logistics): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');
        $expired = 0;
        Order::query()
            ->where('status', OrderStatus::Pending->value)
            ->where('created_at', '<', now()->subHours($hours))
            ->chunkById(100, function ($orders) use ($logistics,$dryRun, &$expired) {
                foreach ($orders as $order) {
                    if (! $dryRun) {
                        $logistics->transition($order, OrderStatus::Expired);
                     }
                     
                     $this->line("Order {$order->id} ({$order->customer_name})");

                    $expired++;
                }
            });

        $this->info($dryRun ? "{$expired} order(s) would expire." : "{$expired} order(s) expired.");

        return Command::SUCCESS;
    }
}
