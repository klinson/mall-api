<?php

namespace App\Console\Commands;

use App\Models\LotteryChance;
use App\Models\Prize;
use App\Models\User;
use Illuminate\Console\Command;

class TestLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:lottery {--times=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试抽奖';

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
        $times = intval($this->option('times'));
        if ($times <= 0) {
            $this->error('number 参数不合法');
            return 0;
        }

        $start_time = microtime(true);
        $prizes = Prize::lotteryTest($times);
        $end_time = microtime(true);

        $this->table(['id', '奖品', '权值', '中奖次数'], $prizes);
        $all_time = $end_time-$start_time;

        $this->info('共计抽奖'.$times.'次，耗时'.($all_time).'s, 平均耗时'.($all_time/$times).'s');
    }
}
