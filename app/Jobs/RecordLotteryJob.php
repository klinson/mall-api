<?php

namespace App\Jobs;

use App\Models\LotteryChance;
use App\Models\LotteryRecord;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 记录中奖记录
 * Class RecordLotteryJob
 * @package App\Jobs
 * @author klinson <klinson@163.com>
 */
class RecordLotteryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prize;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, $prize)
    {
        $this->prize = $prize;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chance = LotteryChance::getChance($this->user_id);
        if (empty($chance)) {
            throw new \Exception('无可用抽奖机会');
        }
        if (! $chance->setUsed()) {
            throw new \Exception('设置使用失败');
        }

        LotteryRecord::generateRecord($this->user_id, $this->prize, $chance);
    }

    public function tags()
    {
        return ['AddLotteryChanceJob', 'user_id:'.$this->user_id, 'prize_id:'.$this->prize->id];
    }
}
