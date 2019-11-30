<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/10/15
 * Time: 00:56
 */

namespace App\Http\Controllers\Api;


use App\Models\Express;
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

    // 发起退款，仅支持确认到货7天内的未申请(1)或已经撤销申请(0)情况
    // test是用于计算实际能退多少
    public function store(Order $order, OrderGoods $orderGoods, Request $request)
    {
        $this->authorize('is-mine', $order);

        if ($request->test) {
            $rule = [
                'quantity' => 'required|numeric',
            ];
        } else {
            $rule = [
                'quantity' => 'required|numeric',
                'reason_text' => 'required|max:250',
                'reason_images' => 'required'
            ];
        }

        $this->validate($request, $rule, [], [
            'quantity' => '退款数量',
            'reason_text' => '退款原因',
            'reason_images' => '说明图片'
        ]);

        try {
            $real_price = RefundOrder::settleRefundPrice($order, $orderGoods, $request->quantity, $request->test ? true : false, true);
            if ($request->test) {
                return $this->response->array([
                    'real_price' => $real_price,
                ]);
            }
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
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
            'real_price' => $real_price,
            'status' => 1,
            'marketing_type' => $orderGoods->marketing_type,
            'marketing_id' => $orderGoods->marketing_id,
        ];
        if ($order->used_balance) {
            $data['real_refund_balance'] = $data['real_price'];
        } else {
            $data['real_refund_cost'] = $data['real_price'];
        }
        $refund_order = $order->refunds()->create($data);

        return $this->response->item($refund_order, new RefundOrderTransformer());
    }

    // 用户发货
    public function express(RefundOrder $order, Request $request)
    {
        $this->authorize('is-mine', $order);

        if ($order->status !== 2) {
            return $this->response->errorBadRequest('订单状态异常，请确认订单状态');
        }

        $this->validate($request, [
            'freight_price' => 'required',
            'express_id' => 'required',
            'express_number' => 'required',
            'mobile' => 'required'
        ], [], [
            'freight_price' => '快递费',
            'mobile' => '联系电话',
            'express_id' => '快递公司',
            'express_number' => '快递单号'
        ]);

        $order->fill($request->only(['freight_price', 'express_number', 'express_id', 'mobile']));
        $order->freight_price = to_int($request->freight_price);
        $order->expressed_at = date('Y-m-d H:i:s');
        $order->status = 3;
        $order->save();

        return $this->response->item($order, new RefundOrderTransformer());
    }

    public function show(RefundOrder $order)
    {
        $this->authorize('is-mine', $order);

        return $this->response->item($order, new RefundOrderTransformer());
    }

    // 撤销申请,已发货(3)，已退款(4)和已拒绝退款(5),已撤销（7）是不可撤销的
    public function repeal(RefundOrder $order)
    {
        $this->authorize('is-mine', $order);

        if (in_array($order->status, [3, 4, 5, 7])) {
            return $this->response->errorBadRequest('售后订单已发货或已完成，不可撤销');
        }

        $order->status = 7;
        $order->save();

        return $this->response->item($order, new RefundOrderTransformer());
    }

    // 更新，仅支持确认到货7天内的未审批(1)和已驳回申请(6)的状态下可以更新
    public function update(RefundOrder $order, Request $request)
    {
        $this->authorize('is-mine', $order);

        if (! in_array($order->status, [1, 6])) {
            return $this->response->errorBadRequest('售后订单状态不可编辑');
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

        try {
            $real_price = RefundOrder::settleRefundPrice($order->order, $order->orderGoods, $request->quantity, false, false);
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
        $data = $request->only(['quantity', 'reason_text', 'reason_images']);

        $data['real_price'] = $real_price;
        $data['status'] = 1;

        if ($order->used_balance) {
            $data['real_refund_balance'] = $data['real_price'];
        } else {
            $data['real_refund_cost'] = $data['real_price'];
        }

        $order->fill($data);
        $order->save();

        return $this->response->item($order, new RefundOrderTransformer());
    }

    // 获取物流信息，仅支持3已发货待卖家确认到货，4已退款，5确认到货拒绝退款
    public function logistics(RefundOrder $order)
    {
        $this->authorize('is-mine', $order);

        if (! in_array($order->status, [3, 4, 5])) {
            return $this->response->errorBadRequest('未发货，无法查询');
        }

        try {
            $res = $order->getLogistics();

            if (isset($res['status']) && $res['status'] == 200) {
                $res['com_name'] = Express::getNameByCode($res['com']);
                return $this->response->array($res);
            } else if (isset($res['result']) && $res['result'] == false) {
                return $this->response->errorBadRequest($res['message']);
            } else {
                return $this->response->errorBadRequest('获取物流失败');
            }
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest('获取物流失败');
        }
    }
}