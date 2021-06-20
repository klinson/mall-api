<?php

namespace App\Admin\Controllers;

use App\Models\OfflineOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OfflineOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '线下订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OfflineOrder);
        $grid->model()->recent();

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
        grid_display_relation($grid, 'staff', 'nickname');
        grid_display_relation($grid, 'store');
        $grid->column('all_price', __('All price'))->currency()->sortable();
        $grid->column('member_discount_price', __('Member discount Price'))->currency()->sortable();
        $grid->column('used_integral', __('Used integral'))->display(function ($item) {
            if (empty($item)) return 0;
            else return "￥".($this->integral_price*0.01)." ({$this->used_integral}分)";
        });
        $grid->column('real_price', __('Real price'))->currency()->sortable();
        $grid->column('used_balance', __('Used balance'))->currency()->sortable();
        $grid->column('real_cost', __('Real cost'))->currency()->sortable();
        $grid->column('status', __('Status'))->using(OfflineOrder::status_text)->filter(OfflineOrder::status_text);
        $grid->column('remarks', __('Remarks'));
        $grid->column('confirmed_at', __('Confirmed at'))->sortable();
        $grid->column('payed_at', __('Payed at'))->sortable();
        $grid->column('created_at', __('Created at'))->sortable();

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('order_number', __('Order number'));
        });
        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
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
        $show = new Show(OfflineOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'staff', 'nickname');
        show_display_relation($show, 'store');
        $show->field('all_price', __('All price'))->currency();
        $show->field('member_discount_price', __('Member discount Price'))->currency();
        $show->field('used_integral', __('Used integral'));
        $show->field('used_integral', __('Used integral'))->as(function ($item) {
            if (empty($item)) return 0;
            else return "￥".($this->integral_price*0.01)." ({$this->used_integral}分)";
        });
        $show->field('real_price', __('Real price'))->currency();
        $show->field('used_balance', __('Used balance'))->currency();
        $show->field('real_cost', __('Real cost'))->currency();
        $show->field('status', __('Status'))->using(OfflineOrder::status_text);
        $show->field('remarks', __('Remarks'));
        $show->field('confirmed_at', __('Confirmed at'));
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
        $form = new Form(new OfflineOrder);

        $form->text('order_number', __('Order number'));
        $form->number('user_id', __('User id'));
        $form->number('staff_id', __('Staff id'));
        $form->number('store_id', __('Store id'));
        $form->number('all_price', __('All price'));
        $form->number('used_integral', __('Used integral'));
        $form->number('real_price', __('Real price'));
        $form->number('used_balance', __('Used balance'));
        $form->number('real_cost', __('Real cost'));
        $form->switch('status', __('Status'));
        $form->datetime('confirmed_at', __('Confirmed at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('payed_at', __('Payed at'))->default(date('Y-m-d H:i:s'));
        $form->text('remarks', __('Remarks'));

        return $form;
    }
}
