<?php

namespace App\Console\Commands;

use App\Models\LotteryChance;
use App\Models\User;
use Illuminate\Console\Command;

class ResetLotteryChanceCountCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-cache:lottery-chance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置抽奖次数缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LotteryChance::resetRedisCacheCount();
        $this->info('重置完成');
    }
}
