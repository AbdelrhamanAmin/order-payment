<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Exceptions\OrderProcessingException;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly Order $orderModel
    ) {}

    public function createOrder(array $data): Order
    {
        return $this->orderModel->create($data);
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        return $this->orderModel->where('id', $orderId)->update(['status' => $status]);
    }

    public function findOrderById(int $orderId): ?Order
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new OrderProcessingException("Order ID {$orderId} not found.");
        }

        return $order;
    }
}
