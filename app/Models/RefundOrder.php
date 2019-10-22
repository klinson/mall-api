<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'order_id', 'order_goods_id', 'order_number', 'user_id', 'goods_id', 'goods_specification_id', 'reason_text', 'reason_images', 'quantity', 'price', 'real_price', 'real_refund_cost', 'real_refund_balance', 'freight_price', 'status', 'reject_reason', 'refund_order_number', 'express_id', 'express_number', 'expressed_at', 'confirmed_at'
    ];

    //状态，0已撤销，1申请中，2通过待买家发货，3已发货待卖家确认到货，4已退款，5确认到货拒绝退款，6申请后直接拒绝
    const status_text = [
        '已撤销', '待审核', '待买家发货', '待确认到货', '已退款', '拒绝退款', '驳回审核'
    ];

    protected static function boot()
    {
        self::saved(function ($model) {
             OrderGoods::where('id', $model->order_goods_id)->update(['refund_status' => $model->status]);

             // 所有都退款需要标记订单已退款
             if ($model->status == 4) {
                 $is_all_refund = 1;
                 foreach ($model->order->orderGoods as $order_good) {
                     if ($order_good->refund_status != 4) {
                         $is_all_refund = 0;
                     }
                 }
                 if ($is_all_refund) {
                     $model->order->stutus = 7;
                     $model->order->save();
                 }
             }
        });
        parent::boot();
    }

    protected $casts = [
        'reason_images' => 'array'
    ];

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderGoods()
    {
        return $this->belongsTo(OrderGoods::class);
    }

    // 执行退款逻辑
    public function refund()
    {
        if ($this->order->used_balance) {
            // 退余额
            $this->order->user->wallet->increment('balance', $this->real_price);
            $this->order->user->wallet->save();
            $this->order->user->wallet->log($this->real_price, $this, "订单（{$this->order->order_number}）的售后订单（{$this->order_number}）退款退回余额");
        } else {
            // 退微信
            if (! app()->isLocal()) {
                $this->refund_order_number = self::generateOrderNumber();
                $app = app('wechat.payment');
                $result = $app->refund->byOutTradeNumber($this->order->order_number, $this->refund_order_number, $this->order->real_cost, $this->real_refund_cost, [
                    // 可在此处传入其他参数，详细参数见微信支付文档
                    'refund_desc' => "订单【{$this->order->order_number}】中商品退款成功",
                ]);
                Log::info("[wechat][payment][refund][{$this->order->order_number}][{$this->refund_order_number}]微信支付退款：" . json_encode($result, JSON_UNESCAPED_UNICODE));

                if (($result['return_code'] ?? 'FAIL') == 'FAIL') {
                    Log::error("[wechat][payment][refund][{$this->order->order_number}][{$this->refund_order_number}]微信支付退款失败：[{$result['return_code']}]{$result['return_msg']}");
                    throw new \Exception('退款失败，' . $result['return_msg'], 200);
                }
                if (($result['result_code'] ?? 'FAIL') == 'FAIL') {
                    Log::error("[wechat][payment][refund][{$this->order->order_number}][{$this->refund_order_number}]微信支付退款失败：[{$result['err_code']}]{$result['err_code_des']}");
                    throw new \Exception('退款失败，' . $result['err_code_des'], 200);
                }
                Log::error("[wechat][payment][refund][{$this->order->order_number}][{$this->refund_order_number}]微信支付退款成功：{$this->real_refund_cost}");
            } else {
                // 本地直接成功
                Log::error("[wechat][payment][refund][{$this->order->order_number}]微信支付local环境退款成功：{$this->real_refund_cost}");
            }
        }
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
            $rev_phone = config('system.express_address.mobile', null);
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
}
