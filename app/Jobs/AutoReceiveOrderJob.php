<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutoReceiveOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order_id;

    /**
     * 定时去自动确认签收
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::find($this->order_id);
        if ($order && $order->status === 3) {
            $order->receive();
        }
    }

    public function tags()
    {
        return ['AutoReceiveOrderJob', 'Order:'.$this->order_id];
    }
}
