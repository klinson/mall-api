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

    const type2methods = [
        LotteryChance::FIRST_LOGIN_TYPE => 'whenUserFirstLogin',
        LotteryChance::INVITE_USER_REGISTER_TYPE => 'whenInviteUserRegister'
    ];

    protected $user_id;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, int $type)
    {
        $this->type = $type;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $method = self::type2methods[$this->type];
        LotteryChance::$method($this->user_id);
    }
}
