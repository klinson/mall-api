<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\AjaxWithInputButton;
use App\Admin\Extensions\Tools\DefaultBatchTool;
use App\Models\Order;
use App\Models\RefundOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->model()->with(['orderGoods'])->recent();

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'))->expand(function () {
            $goods = $this->orderGoods->map(function ($item) {
                return [
                    'goods_title' => $item->snapshot['goods']['title'],
                    'goods_specification_title' => $item->snapshot['title'],
                    'price' => $item->price * 0.01 . ' 元',
                    'quantity' => $item->quantity,
                    'is_refund' => RefundOrder::status_text[$item->refund_status]
                ];
            })->toArray();

            return new Table([
                '商品名称',
                '商品规格',
                '单价',
                '数量',
                '退款？',
            ], $goods);
        });
        grid_display_relation($grid, 'user', 'nickname');
        $grid->column('all_price', __('All price'))->currency();
        $grid->column('goods_price', __('Goods price'))->currency();
        $grid->column('real_price', __('Real price'))->currency();
        $grid->column('coupon_price', __('Coupon price'))->currency();
        $grid->column('freight_price', __('Freight price'))->currency();
        $grid->column('remarks', __('Remarks'));
        $grid->column('status', __('Status'))->using(Order::status_text)->filter(Order::status_text);
        $grid->column('created_at', __('Created at'));
        $grid->column('payed_at', __('Payed at'));

        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            if ($this->row->status === 2) {
                $actions->append(new AjaxWithInputButton(
                    $actions->getResource() . '/' . $actions->getKey() . '/express',
                    '发货',
                    'express_number',
                    '请输入快递单号'
                ));
            }
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
//                $batch->disableDelete();
                $batch->add('批量取消订单', new DefaultBatchTool('cancel'));
                // 暂时未对接快递，无法自动批量发货
//                $batch->add('批量确认发货', new DefaultBatchTool('express'));
            });
        });

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('order_number', __('Order number'));
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'user', 'nickname');
        show_display_relation($show, 'address', 'address');
        $show->field('all_price', __('All price'))->currency();
        $show->field('goods_price', __('Goods price'))->currency();
        $show->field('real_price', __('Real price'))->currency();
        $show->field('coupon_price', __('Coupon price'))->currency();
        $show->field('freight_price', __('Freight price'))->currency();
        $show->field('remarks', __('Remarks'));
        $show->field('status', __('Status'))->using(Order::status_text);
        $show->field('created_at', __('Created at'));
        $show->field('payed_at', __('Payed at'));
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
        $form = new Form(new Order);

        $form->display('id');

        return $form;
    }

    // 确认发货
    public function batchExpress(Request $request)
    {
        $orders = Order::where('status', 2)
            ->whereIn('id', $request->ids)
            ->get();
        if (! $orders->isEmpty()) {
            foreach ($orders as $order) {
                $order->express();
            }
        }

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    public function express(Order $order, Request $request)
    {
        if ($order->status !== 2) {
            $data = [
                'status'  => false,
                'message' => '订单状态异常',
            ];
            return response()->json($data);
        }

        if (empty($request->express_number)) {
            $data = [
                'status'  => false,
                'message' => '请输入快递单号',
            ];
            return response()->json($data);
        }

        $order->expressing($request->express_number);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    // 确认发货
    public function cancel(Request $request)
    {
        $orders = Order::whereIn('status', [1, 2, 3, 4])
            ->whereIn('id', $request->ids)
            ->get();
        if (! $orders->isEmpty()) {
            $info = [];
            $code = 0;
            foreach ($orders as $order) {
                try {
                    $order->cancel();
                    $info[] = "No.{$order->id}：{$order->order_number} 取消成功";
                } catch (\Exception $exception) {
                    $code = 1;
                    $info[] = "No.{$order->id}：{$order->order_number} 取消失败，{$exception->getMessage()}";
                }
            }
            if (! $code) {
                admin_success('处理成功', implode("<br/>", $info));
            } else {
                admin_warning('处理完成，存在失败，请勿频繁重试', implode("<br/>", $info));
            }
        }

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }
}
