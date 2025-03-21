<?php

namespace App\Services;

use App\Constants\Constants;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OrderProcessingException;
use App\Repositories\OrderRepository;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ) {}

    public function processOrderPayment(int $orderId)
    {
        try {
            // $order = $this->orderRepository->findOrderById($orderId);

            // Update status to processing
            $this->orderRepository->updateOrderStatus($orderId, Constants::PROCESSING_STATUS);
            Log::info("Processing started for Order ID {$orderId}.");

            // Simulates an external payment API call.
            sleep(2);

            // Randomly marks the order as completed or failed.
            $responseStatus = random_int(0, 1) ? Constants::COMPLETED_STATUS : Constants::FAILED_STATUS;
            if ($responseStatus === Constants::COMPLETED_STATUS) {
                Log::info("Order ID {$orderId} payment successfully completed.");
            } else {
                Log::error("Order ID {$orderId} payment failed.");
            }

            // Update order status
            $this->orderRepository->updateOrderStatus($orderId, $responseStatus);
        } catch (OrderProcessingException $exception) {
            Log::error("Order Processing Exception: {$exception->getMessage()}");
        }
    }
}
