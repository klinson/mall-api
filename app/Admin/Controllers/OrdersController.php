<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\DefaultBatchTool;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
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

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'user', 'nickname');
        $grid->column('all_price', __('All price'))->currency();
        $grid->column('goods_price', __('Goods price'))->currency();
        $grid->column('real_price', __('Real price'))->currency();
        $grid->column('coupon_price', __('Coupon price'))->currency();
        $grid->column('freight_price', __('Freight price'))->currency();
        $grid->column('remarks', __('Remarks'));
        $grid->column('status', __('Status'))->using(Order::status_text);
        $grid->column('created_at', __('Created at'));
        $grid->column('payed_at', __('Payed at'));

        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
//                $batch->disableDelete();
                $batch->add('取消订单', new DefaultBatchTool('cancel'));
                $batch->add('确认发货', new DefaultBatchTool('express'));
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
    public function express(Request $request)
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

    // 确认发货
    public function cancel(Request $request)
    {
        $orders = Order::where('status', '<>', 5)
            ->whereIn('id', $request->ids)
            ->get();
        if (! $orders->isEmpty()) {
            foreach ($orders as $order) {
                $order->cancel();
            }
        }

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }
}
