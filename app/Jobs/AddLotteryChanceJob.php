<?php

namespace App\Jobs;

use App\Models\LotteryChance;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddLotteryChanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, int $event)
    {
        $this->event = $event;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        LotteryChance::whenEvent($this->user_id, $this->event);
    }

    public function tags()
    {
        return ['AddLotteryChanceJob', 'user_id:'.$this->user_id, 'event:'.$this->event];
    }
}
