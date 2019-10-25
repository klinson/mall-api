<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\DefaultBatchTool;
use App\Models\RefundOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use DB;

class RefundOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单售后管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RefundOrder);
        $grid->model()->with(['orderGoods', 'owner', 'order'])->recent();

        $grid->column('id', __('Id'));
        $grid->column('order_number', '退款订单号');
        grid_display_relation($grid, 'owner', 'nickname');
        grid_display_relation($grid, 'order', 'order_number');
        $grid->column('orderGoods', '退款商品')->display(function ($item) {
            return $this->orderGoods->toString();
        });

        $grid->column('quantity', __('Quantity'))->sortable()->filter('range');
        $grid->column('price', __('Price'))->currency();
        $grid->column('real_price', '实际应退')->currency();
        $grid->column('freight_price', __('Freight price'))->currency();
        $grid->column('status', __('Status'))->using(RefundOrder::status_text)->filter(RefundOrder::status_text)->filter(RefundOrder::status_text);
        $grid->column('reason_text', __('Reason text'));
        $grid->column('reason_images', __('Reason Images'))->image();
        $grid->column('expressed_at', __('Expressed at'))->sortable()->filter('range', 'datetime');
        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
//                $batch->disableDelete();
                $batch->add('批量审核通过', new DefaultBatchTool('batch/pass'));
                $batch->add('批量审核不通过', new DefaultBatchTool('batch/reject'));
                $batch->add('批量退款', new DefaultBatchTool('batch/refund'));
                $batch->add('批量拒绝退款', new DefaultBatchTool('batch/rejectRefund'));
                $batch->add('批量撤销申请', new DefaultBatchTool('batch/repeal'));
            });
        });

        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('order_number', __('Order number'));
            $filter->like('express_number', __('Express number'));
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(RefundOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', '退款订单号');
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'order', 'order_number');
        $show->field('orderGoods', '退款商品')->as(function ($item) {
            return $this->orderGoods->toString();
        });

        $show->field('quantity', __('Quantity'));
        $show->field('price', __('Price'))->currency();
        $show->field('real_price', __('Real price'))->currency();
        $show->field('real_refund_cost', __('Real refund cost'))->currency();
        $show->field('real_refund_balance', __('Real refund balance'))->currency();
        $show->field('freight_price', __('Freight price'))->currency();
        $show->field('status', __('Status'))->using(RefundOrder::status_text);
        $show->field('reason_text', __('Reason text'));
        $show->field('reason_images', __('Reason images'))->image();
        $show->field('refund_order_number', '微信商户退款订单号');
        $show->field('expressed_at', __('Expressed at'));
        $show->field('confirmed_at', __('Confirmed at'));
        show_display_relation($show, 'express', 'name');
        $show->field('express_number', __('Express number'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RefundOrder);

        $form->text('order_number', __('Order number'));
        $form->number('user_id', __('User id'));
        $form->number('order_id', __('Order id'));
        $form->number('order_goods_id', __('Order goods id'));
        $form->number('goods_id', __('Goods id'));
        $form->number('goods_specification_id', __('Goods specification id'));
        $form->number('quantity', __('Quantity'));
        $form->number('price', __('Price'));
        $form->number('real_price', __('Real price'));
        $form->number('real_refund_cost', __('Real refund cost'));
        $form->number('real_refund_balance', __('Real refund balance'));
        $form->number('freight_price', __('Freight price'));
        $form->switch('status', __('Status'));
        $form->text('reason_text', __('Reason text'));
        $form->text('reason_images', __('Reason images'));
        $form->text('refund_order_number', __('Refund order number'));
        $form->datetime('expressed_at', __('Expressed at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('confirmed_at', __('Confirmed at'))->default(date('Y-m-d H:i:s'));
        $form->number('express_id', __('Express id'));
        $form->text('express_number', __('Express number'));

        return $form;
    }

    /**
     * 批操作
     * @param Request $request
     * @param string $handle 确认通过-pass,确认拒绝reject,撤销申请repeal,拒绝退款rejectRefund
     * @return \Illuminate\Http\JsonResponse
     * @author klinson <klinson@163.com>
     */
    public function batch(Request $request, $handle)
    {
        $orders = RefundOrder::whereIn('id', $request->ids)->get();

        $info = [];
        $error_count = 0;
        foreach ($orders as $order) {
            if ($order->$handle()) {
                $info[] = "No.{$order->id}：{$order->order_number} 处理成功";
            } else {
                $error_count++;
                $info[] = "No.{$order->id}：{$order->order_number} 处理失败";
            }
        }

        return show_batch_result($error_count, $info);
    }


    // 确认退款
    public function batchRefund(Request $request)
    {
        $orders = RefundOrder::where('status', 3)
            ->whereIn('id', $request->ids)
            ->get();

        $info = [];
        $error_code = 0;
        foreach ($orders as $order) {
            try {
                DB::beginTransaction();

                $order->status = 4;
                $order->confirmed_at = date('Y-m-d H:i:s');
                $order->save();

                $order->refund();
                DB::commit();
                $info[] = "No.{$order->id}：{$order->order_number} 退款成功";
            } catch (\Exception $exception) {
                if ($order->used_balance) {
                    $error_code === 0 && $error_code = 1;
                    $info[] = "No.{$order->id}：{$order->order_number} 退款失败，请重试";
                } else {
                    $error_code = 2;
                    $info[] = "No.{$order->id}：{$order->order_number} 退款异常，请联系管理员，错误信息：{$exception->getMessage()}";
                }

                DB::rollBack();
            }
        }

        switch ($error_code) {
            case 1:
                admin_warning('处理完成，存在失败', implode("<br/>", $info));
                break;
            case 2:
                admin_error('处理存在异常，请不要反复操作，请联系管理员', implode("<br/>", $info));
                break;
            case 0:
            default:
            admin_success('处理成功', implode("<br/>", $info));
                break;

        }

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }
}
