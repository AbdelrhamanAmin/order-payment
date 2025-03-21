<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function createOrder(array $data): Order;
    public function updateOrderStatus(int $orderId, string $status): bool;
    public function findOrderById(int $orderId): ?Order;
}
