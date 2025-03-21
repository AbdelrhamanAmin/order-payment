<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Jobs\ProcessOrderPayment;
use Illuminate\Support\Facades\Log;

class ProcessOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:process-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all pending orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', 'pending')->get();

        if ($orders->isEmpty()) {
            $this->info('No pending orders found.');
            return;
        }

        $orders->each(function ($order) {
            ProcessOrderPayment::dispatch($order->id);
            Log::info("Dispatched order #{$order->id} for processing.");
        });

        $this->info(count($orders) . ' pending orders dispatched successfully.');

    }
}
