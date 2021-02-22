<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WalletActivity extends Model
{
    use SoftDeletes;

    public static function getValidActivities()
    {
        $list = WalletActivity::enabled()->orderBy('present', 'desc')->orderBy('threshold', 'asc')->get();
        return $list;
    }

    /**
     * 计算充值可以获得多少钱
     * @param $balance
     * @return array|false ['balance' => '支付金额', 'activity' => '充值活动', 'result' => '预计到账金额']
     * @author klinson <klinson@163.com>
     */
    public static function calculateRecharge($balance)
    {
        if ($balance <= 0) return false;
        $list = static::getValidActivities();
        $activity = null;
        $result = $balance;
        foreach ($list as $item) {
            if ($item->threshold <= $balance) {
                $activity = $item;
                $result += $item->present;
                break;
            }
        }
        return [
            'balance' => $balance,
            'activity' => $activity,
            'result' => $result,
        ];
    }
}
