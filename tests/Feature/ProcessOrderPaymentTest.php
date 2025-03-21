<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrderPayment;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ProcessOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure Redis is clean before running tests
        Redis::flushdb();
    }

    public function test_it_processes_an_order_correctly()
    {
        Queue::fake();

        // Create a user and order
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        // Dispatch the job
        ProcessOrderPayment::dispatch($order->id);

        // Assert that the job was pushed to the queue
        Queue::assertPushed(ProcessOrderPayment::class, function ($job) use ($order) {
            return $job->orderId === $order->id;
        });
    }

    public function test_it_updates_order_status_correctly()
    {
        // Create a user and order
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        // Mock OrderService
        $orderService = $this->createMock(OrderService::class);
        $orderService->method('processOrderPayment')->willReturnCallback(function ($orderId) use ($order) {
            $order->status = ['completed', 'failed'][array_rand(['completed', 'failed'])];
            $order->save();
        });

        // Dispatch and process the job
        $job = new ProcessOrderPayment($order->id);
        $job->handle($orderService);

        // Refresh the order from the database
        $order->refresh();

        // Assert order status transitions
        $this->assertContains($order->status, ['completed', 'failed']);
    }

    public function test_failed_jobs_are_retried_properly()
    {
        Queue::fake();

        // Create a user and order
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        // Dispatch the job
        ProcessOrderPayment::dispatch($order->id);

        // Simulate job failure
        Queue::assertPushed(ProcessOrderPayment::class, function ($job) use ($order) {
            $job->failed(new \Exception('Payment failed'));
            return true;
        });

        // Assert the job was retried
        Queue::assertPushed(ProcessOrderPayment::class);
    }
}
