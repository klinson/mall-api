<?php

namespace App\Admin\Controllers;

use App\Models\RechargeThresholdOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RechargeThresholdOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '充值订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RechargeThresholdOrder);

        $grid->model()->where('status', 2)->recent();

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
//        $grid->column('agency_config_id', __('Agency config id'));
        $grid->column('wallet_activity_snapshot', __('Wallet activity snapshot'))->display(function ($item) {
            return $item ? $item['title'] : '';
        });
        $grid->column('balance', __('Balance'))->currency();
        $grid->column('result', __('Recharge threshold order result'))->currency();
        $grid->column('status', __('Status'))->using(RechargeThresholdOrder::status_text);
        $grid->column('payed_at', __('Payed at'));
        $grid->column('created_at', __('Created at'));
        grid_display_relation($grid, 'datatype', 'description', '关联日志');

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->disableRowSelector();
        $grid->filter(function (Grid\Filter $filter) {
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
        $show = new Show(RechargeThresholdOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'owner', 'nickname');
//        $show->field('agency_config_id', __('Agency config id'));
        show_display_relation($show, 'datatype', 'description', '关联日志');
        $show->field('wallet_activity_snapshot', __('Wallet activity snapshot'))->as(function ($item) {
            return $item ? $item['title'] : '';
        });
        $show->field('balance', __('Balance'))->currency();
        $show->field('result', __('Recharge threshold order result'))->currency();
//        $show->field('wallet_activity_id', __('Wallet activity id'));
        $show->field('status', __('Status'))->using(RechargeThresholdOrder::status_text);
        $show->field('payed_at', __('Payed at'));
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
        $form = new Form(new RechargeThresholdOrder);

        $form->text('order_number', __('Order number'));
        $form->number('user_id', __('User id'));
        $form->number('agency_config_id', __('Agency config id'));
        $form->number('balance', __('Balance'));
        $form->switch('status', __('Status'));
        $form->datetime('payed_at', __('Payed at'))->default(date('Y-m-d H:i:s'));
        $form->number('result', __('Result'));
        $form->number('wallet_activity_id', __('Wallet activity id'));
        $form->text('wallet_activity_snapshot', __('Wallet activity snapshot'));

        return $form;
    }
}
