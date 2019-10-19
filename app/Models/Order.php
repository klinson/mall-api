<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Order extends Model
{
    use SoftDeletes;

    const wechat_pay_notify_route = 'order.wechat.pay.notify';

    // 1待支付，2已支付待发货，3已发货待收货，4已收货交易完成，5取消订单，6超时过期，7退款
    const status_text = [
        '未知', '待支付', '待发货', '待收货', '已完成', '已取消', '超时过期', '已退款'
    ];

    public static function getWechatPayNotifyUrl()
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route(self::wechat_pay_notify_route);
    }

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(['nickname' => '[已删除]', 'mobile' => '']);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id')->withDefault(['address' => '[已删除]', 'mobile' => '']);
    }

    /**
     * 获取快递100物流信息
     * @author klinson <klinson@163.com>
     * @return mixed
     */
    public function getLogistics()
    {
        $config = config('services.kuaidi100');
        $express = new \Puzzle9\Kuaidi100\Express($config['key'], $config['customer'], $config['callbackurl']);

        if ($this->express->code === 'shunfeng') {
            $rev_phone = $this->address->mobile;
        } else {
            $rev_phone = null;
        }

        //实时查询
        $list = $express->synquery($this->express->code, $this->express_number, $rev_phone); // 快递服务商 快递单号 手机号
        return $list;
    }

    public function express()
    {
        return $this->belongsTo(Express::class, 'express_id', 'id');
    }

    public function specifications()
    {
        return $this->belongsToMany(GoodsSpecification::class, 'order_goods', 'order_id', 'goods_specification_id', 'id', 'id')->withPivot(['quantity']);
    }

    public function resetSpecificationQuantity()
    {
        foreach ($this->specifications as $specification) {
            $specification->sold($specification->pivot_quantity, true);
        }
    }

    // 取消订单
    public function cancel()
    {
        if (! in_array($this->status, [1, 2, 3, 4])) {
            throw new \Exception('订单状态无法取消');
        }

        if ($this->status == 1) {
            try {
                \DB::beginTransaction();

                $this->status = 5;
                $this->save();

                // 恢复库存
                $this->resetSpecificationQuantity();

                \DB::commit();
                return $this;
            } catch (\Exception $exception) {
                DB::rollBack();
                throw new \Exception('退款失败');
            }
        }

        try {
            \DB::beginTransaction();

            if ($this->used_balance > 0) {
                $this->user->wallet->increment('balance', $this->used_balance);
                $this->user->wallet->save();
                $this->user->wallet->log($this->used_balance, $this, "取消订单（{$this->order_number}）退款入账");
            } else {
                $this->cancel_order_number = self::generateOrderNumber();
                if (! app()->isLocal()) {
                    $app = app('wechat.payment');
                    $result = $app->refund->byOutTradeNumber($this->order_number, $this->cancel_order_number, $this->real_cost, $this->real_cost, [
                        // 可在此处传入其他参数，详细参数见微信支付文档
                        'refund_desc' => "用户本人操作订单【{$this->order_number}】进行商品订单取消并进行退款",
                    ]);
                    Log::info("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款：" . json_encode($result, JSON_UNESCAPED_UNICODE));

                    if (($result['return_code'] ?? 'FAIL') == 'FAIL') {
                        Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款失败：[{$result['return_code']}]{$result['return_msg']}");
                        throw new \Exception('退款失败，' . $result['return_msg'], 200);
                    }
                    if (($result['result_code'] ?? 'FAIL') == 'FAIL') {
                        Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款失败：[{$result['err_code']}]{$result['err_code_des']}");
                        throw new \Exception('退款失败，' . $result['err_code_des'], 200);
                    }
                    Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款成功：{$this->real_cost}");
                } else {
                    // 本地直接成功
                    Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付local环境退款成功：{$this->real_cost}");
                }
            }

            $this->status = 5;
            $this->save();

            \DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            if ($exception->getCode() != 200) {
                Log::error("[order][refund][error][{$this->order_number}]订单取消异常，{$exception->getMessage()}，{$exception->getCode()}，{$exception->getFile()}，{$exception->getLine()}");

                throw new \Exception('退款失败');
            } else {
                throw new \Exception($exception->getMessage());
            }
        }
    }

    // 发货
    public function expressing($express_number = null)
    {
        $this->status = 3;
        $this->expressed_at = date('Y-m-d H:i:s');
        $this->express_id = config('system.express_company_id', 0);
        $this->express_number = $express_number ?: '';

        $this->save();
        // TODO: 日志记录
        // TODO: 通知
    }

    /**
     * 支付
     * @param int $used_balance
     * @author klinson <klinson@163.com>
     */
    public function pay($used_balance = 0)
    {
        // 直接支付成功
        $this->used_balance = $used_balance;
        if ($used_balance >= $this->real_price) {
            $this->real_cost = 0;
        } else {
            $this->real_cost = $this->real_price - $used_balance;
        }
        $this->status = 2;
        $this->payed_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function receive()
    {
        $this->status = 4;
        $this->confirmed_at = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * 订单状态统计
     * @param $user
     * @author klinson <klinson@163.com>
     * @return array
     */
    public static function statusCount($user)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        $res = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('user_id', $user_id)
            ->groupBy('status')
            ->select(['status', \DB::raw('count(*) as total_count')])
            ->orderBy('status')
            ->get();

        $return = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
        ];

        if ($res->isEmpty()) {
            return $return;
        }

        foreach ($res as $re) {
            $return[$re->status] = $re->total_count;
            $return[0] += $re->total_count;
        }

        return $return;
    }

    public function refunds()
    {
        return $this->hasMany(RefundOrder::class, 'order_id', 'id');
    }
}
