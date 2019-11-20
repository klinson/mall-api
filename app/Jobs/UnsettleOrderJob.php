<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UnsettleOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $rate;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, int $rate)
    {
        $this->order = $order;
        $this->rate = $rate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->unsettle($this->rate)) {
            dispatch(new SettleOrderJob($this->order, $this->rate))->delay(Carbon::now()->addDays(config('system.order_auto_settle_days', 7)));
        }
    }

    public function tags()
    {
        return ['UnsettleOrderJob', 'order_id:'.$this->order->id, 'rate:'.($this->rate*0.01).'%'];
    }
}
