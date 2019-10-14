<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/10/15
 * Time: 00:56
 */

namespace App\Http\Controllers\Api;


use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\RefundOrder;
use App\Transformers\RefundOrderTransformer;
use Illuminate\Http\Request;

class RefundOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundOrder::query();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }

        $list = $query->isOwner()->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new RefundOrderTransformer());
    }

    public function store(Order $order, OrderGoods $orderGoods, Request $request)
    {
        $this->authorize('is-mine', $order);

        if ($orderGoods->order_id !== $order->id) {
            return $this->response->errorBadRequest('请选择退款商品');
        }

        if ($orderGoods->refund_status) {
            return $this->response->errorBadRequest('该商品已经申请过退款');
        }

        $this->validate($request, [
            'quantity' => 'required|numeric',
            'reason_text' => 'required|max:250',
            'reason_images' => 'required'
        ], [], [
            'quantity' => '退款数量',
            'reason_text' => '退款原因',
            'reason_images' => '说明图片'
        ]);

        if ($orderGoods->quantity < $request->quantity) {
            return $this->response->errorBadRequest('退款数量不合法');
        }


        $data = [
            'order_number' => RefundOrder::generateOrderNumber(),
            'user_id' => $this->user->id,
            'goods_id' => $orderGoods->goods_id,
            'order_goods_id' => $orderGoods->id,
            'goods_specification_id' => $orderGoods->goods_specification_id,
            'reason_text' => $request->reason_text,
            'reason_images' => $request->reason_images,
            'quantity' => $request->quantity,
            'price' => $orderGoods->price,
            // 优惠需按比例扣除
            'real_price' => $request->quantity * $orderGoods->price,
            'status' => 1,
        ];
        if ($order->used_balance) {
            $data['real_refund_balance'] = $data['real_price'];
        } else {
            $data['real_refund_cost'] = $data['real_price'];
        }
        $refund_order = $order->refunds()->create($data);

        return $this->response->item($refund_order, new RefundOrderTransformer());
    }
}