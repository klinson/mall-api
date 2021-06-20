<?php

namespace App\Console\Commands;

use App\Models\OfflineOrder;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OrderSettle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:settle {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结算订单到积分和会员经验';

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
        $type = trim($this->input->getArgument('type'));

        // 积分开关
        $integral_status = config('system.integral_status', 0);

        switch ($type) {
            case 'order':
                $query = Order::query();
                $time_field = 'confirmed_at';
                $status = 4;
                break;
            case 'offline_order':
                $query = OfflineOrder::query();
                $time_field = 'payed_at';
                $status = 3;
                break;
            default:
                return false;
                break;
        }
        $query->with(['owner'])->where('status', $status)
            ->whereBetween($time_field, [
                Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()
            ])->chunk(1000, function ($list) use ($integral_status) {
                foreach ($list as $order) {
                    DB::beginTransaction();
                    try {
                        if ($integral_status) {
                            $order->owner->integral->useIt($order, 1);
                        }
                        $order->owner->score->addScore($order);
                        DB::commit();
                    } catch (\Exception $exception) {
                        DB::rollBack();
                    }
                }
        });
    }
}
