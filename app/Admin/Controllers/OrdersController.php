<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Order';

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
        $grid->column('user_id', __('User id'));
        $grid->column('address_id', __('Address id'));
        $grid->column('all_price', __('All price'));
        $grid->column('goods_price', __('Goods price'));
        $grid->column('real_price', __('Real price'));
        $grid->column('coupon_price', __('Coupon price'));
        $grid->column('freight_price', __('Freight price'));
        $grid->column('remarks', __('Remarks'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));

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
        $show->field('user_id', __('User id'));
        $show->field('address_id', __('Address id'));
        $show->field('all_price', __('All price'));
        $show->field('goods_price', __('Goods price'));
        $show->field('real_price', __('Real price'));
        $show->field('coupon_price', __('Coupon price'));
        $show->field('freight_price', __('Freight price'));
        $show->field('remarks', __('Remarks'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Order);

        $form->text('order_number', __('Order number'));
        $form->number('user_id', __('User id'));
        $form->number('address_id', __('Address id'));
        $form->number('all_price', __('All price'));
        $form->number('goods_price', __('Goods price'));
        $form->number('real_price', __('Real price'));
        $form->number('coupon_price', __('Coupon price'));
        $form->number('freight_price', __('Freight price'));
        $form->text('remarks', __('Remarks'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
