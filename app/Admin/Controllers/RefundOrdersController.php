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

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
        grid_display_relation($grid, 'order', 'order_number');
        $grid->column('quantity', __('Quantity'));
        $grid->column('price', __('Price'));
        $grid->column('real_price', '实际应退');
        $grid->column('freight_price', __('Freight price'));
        $grid->column('status', __('Status'))->using(RefundOrder::status_text);
        $grid->column('reason_text', __('Reason text'));
        $grid->column('expressed_at', __('Expressed at'));
        $grid->column('created_at', __('Created at'));

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
//                $batch->disableDelete();
                $batch->add('批量审核通过', new DefaultBatchTool('pass'));
                $batch->add('批量审核不通过', new DefaultBatchTool('reject'));
                $batch->add('批量退款', new DefaultBatchTool('refund'));
                $batch->add('批量拒绝退款', new DefaultBatchTool('rejectRefund'));
                $batch->add('批量撤销申请', new DefaultBatchTool('repeal'));
            });
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
        $show->field('order_number', __('Order number'));
        $show->field('user_id', __('User id'));
        $show->field('order_id', __('Order id'));
        $show->field('order_goods_id', __('Order goods id'));
        $show->field('goods_id', __('Goods id'));
        $show->field('goods_specification_id', __('Goods specification id'));
        $show->field('quantity', __('Quantity'));
        $show->field('price', __('Price'));
        $show->field('real_price', __('Real price'));
        $show->field('real_refund_cost', __('Real refund cost'));
        $show->field('real_refund_balance', __('Real refund balance'));
        $show->field('freight_price', __('Freight price'));
        $show->field('status', __('Status'));
        $show->field('reason_text', __('Reason text'));
        $show->field('reason_images', __('Reason images'));
        $show->field('refund_order_number', __('Refund order number'));
        $show->field('expressed_at', __('Expressed at'));
        $show->field('confirmed_at', __('Confirmed at'));
        $show->field('express_id', __('Express id'));
        $show->field('express_number', __('Express number'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

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

    // 确认通过
    public function pass(Request $request)
    {
        RefundOrder::where('status', 1)
            ->whereIn('id', $request->ids)
            ->update(['status' => 2]);


        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    // 确认拒绝
    public function reject(Request $request)
    {
        RefundOrder::where('status', 1)
            ->whereIn('id', $request->ids)
            ->update(['status' => 6]);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    // 撤销申请
    public function repeal(Request $request)
    {
        RefundOrder::whereNotIn('status', [3, 4, 5])
            ->whereIn('id', $request->ids)
            ->update(['status' => 0]);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    // 确认退款
    public function refund(Request $request)
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

    // 拒绝退款
    public function rejectRefund(Request $request)
    {
        RefundOrder::where('status', 3)
            ->whereIn('id', $request->ids)
            ->update([
                'status' => 5,
                'confirmed_at' => date('Y-m-d H:i:s'),
        ]);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }
}
