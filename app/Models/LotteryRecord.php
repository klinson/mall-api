<?php

namespace App\Models;

use App\Jobs\AutoReceiveLotteryRecordJob;
use App\Models\Traits\HasOwnerHelper;
use App\Transformers\PrizeTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryRecord extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'user_id', 'prize_id', 'prize_snapshot', 'chance_id', 'status', 'address_snapshot', 'address_id'
    ];

    // 1待发货，2已发货，3已完成
    const status_text = [
        1 => '待发货',
        2 => '已发货',
        3 => '已完成'
    ];

    const express_status_text = [
        0 => '在途', 1 => '揽收', 2 => '疑难', 3 => '签收', 4 => '退签', 5 => '派件', 6 => '退回', -1 => '未知'
    ];

    protected $casts = [
        'prize_snapshot' => 'array',
        'address_snapshot' => 'array',
    ];

    public static function generateRecord($user, Prize $prize, LotteryChance $chance)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }
        $prize->decrement('quantity', 1);
        $prize->save();

        $record = new self([
            'user_id' => $user_id,
            'prize_id' => $prize->id,
            'prize_snapshot' => (new PrizeTransformer())->transform($prize),
            'chance_id' => $chance->id,
            'status' => 1,
            'address_snapshot' => [],
        ]);
        $record->save();

        return $record;
    }

    public function getLogistics()
    {
        if (empty($this->express_id) || empty($this->express_number)) {
            throw new \Exception('该订单未设置物流单号');
        }

        $config = config('services.kuaidi100');
        $express = new \Puzzle9\Kuaidi100\Express($config['key'], $config['customer'], $config['callbackurl']);

        if ($this->express->code === 'shunfeng') {
            $rev_phone = $this->address->mobile;
        } else {
            $rev_phone = null;
        }

        //实时查询 https://www.kuaidi100.com/openapi/api_post.shtml
        $res = $express->synquery($this->express->code, $this->express_number, $rev_phone); // 快递服务商 快递单号 手机号

        if (isset($res['status']) && $res['status'] == 200) {
            $res['com_name'] = Express::getNameByCode($res['com']);

            return $res;
        } else if (isset($res['result']) && $res['result'] == false) {
            throw new \Exception($res['message']);
        } else {
            throw new \Exception('获取物流失败');
        }
    }

    public function express()
    {
        return $this->belongsTo(Express::class, 'express_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id')->withDefault(['address' => '[已删除]', 'mobile' => '']);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    // 发货
    public function expressing($express_number = null, $express_id = 0)
    {
        $this->status = 2;
        $this->expressed_at = date('Y-m-d H:i:s');
        $this->express_id = $express_id;
        $this->express_number = $express_number ?: '';

        $this->save();

        // 定时N天去确认到货
        dispatch(new AutoReceiveLotteryRecordJob($this->id))->delay(Carbon::now()->addDays(config('system.order_auto_receive_days', 7)));
    }

    public function receive()
    {
        $this->status = 3;
        $this->save();
    }
}
