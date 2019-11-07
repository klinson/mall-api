<?php

namespace App\Admin\Controllers;

use App\Models\MemberRechargeOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MemberRechargeOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '会员充值订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MemberRechargeOrder);

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');

        $grid->column('balance', __('Balance'))->currency();
        $grid->column('member_recharge_activity_snapshot', __('Member recharge activity snapshot'))->display(function ($item) {
            return $item['title'];
        });
        $grid->column('member_level_snapshot', __('Member level snapshot'))->display(function ($item) {
            return $item['title'];
        });
        $grid->column('validity_started_at', __('Validity started at'));
        $grid->column('validity_ended_at', __('Validity ended at'))->display(function ($item) {
            return $item ?: '永久';
        });
        $grid->column('status', __('Status'))->using(MemberRechargeOrder::status_text)->filter(MemberRechargeOrder::status_text);
        grid_display_relation($grid, 'inviter', 'nickname');

        $grid->column('payed_at', __('Payed at'));
        $grid->column('created_at', __('Created at'));

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
        $show = new Show(MemberRechargeOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        $show->field('balance', __('Balance'))->currency();
        show_display_relation($show, 'owner', 'nickname');

        show_display_relation($show, 'memberRechargeActivity');
        $show->field('member_recharge_activity_snapshot', __('Member recharge activity snapshot'))->unescape()->array2json();

        show_display_relation($show, 'memberLevel');
        $show->field('member_level_snapshot', __('Member level snapshot'))->unescape()->array2json();

        $show->field('validity_started_at', __('Validity started at'));
        $show->field('validity_ended_at', __('Validity ended at'))->as(function ($item) {
            return $item ?: '永久';
        });
        $show->field('status', __('Status'))->using(MemberRechargeOrder::status_text);
        show_display_relation($show, 'inviter', 'nickname');

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
        $form = new Form(new MemberRechargeOrder);

        return $form;
    }
}
