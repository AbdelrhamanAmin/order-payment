<?php

namespace App\Jobs;

use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $orderId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        $orderService->processOrderPayment($this->orderId);
    }


    public function failed(\Throwable $exception): void
    {
        Log::error("Order processing failed", [
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);
    }
}
