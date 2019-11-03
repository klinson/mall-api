<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use App\Transformers\PrizeTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryRecord extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'user_id', 'prize_id', 'prize_snapshot', 'chance_id', 'status', 'address_snapshot', 'address_id'
    ];

    protected $casts = [
        'prize_snapshot' => 'array',
        'address_snapshot' => 'array',
    ];

    public static function generateRecord(User $user, Prize $prize, LotteryChance $chance)
    {
        $prize->decrement('quantity', 1);
        $prize->save();

        $record = new self([
            'user_id' => $user->id,
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
        return $this->belongsTo(Express::class, 'express_id', 'id')->withDefault([
            'id' => 0,
            'name' => '无'
        ]);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id')->withDefault(['address' => '[已删除]', 'mobile' => '']);
    }

}
