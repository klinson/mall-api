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
    protected $title = 'App\Models\MemberRechargeOrder';

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
        $grid->column('balance', __('Balance'));
        $grid->column('user_id', __('User id'));
        $grid->column('member_recharge_activity_id', __('Member recharge activity id'));
        $grid->column('member_recharge_activity_snapshot', __('Member recharge activity snapshot'));
        $grid->column('member_level_id', __('Member level id'));
        $grid->column('member_level_snapshot', __('Member level snapshot'));
        $grid->column('validity_started_at', __('Validity started at'));
        $grid->column('validity_ended_at', __('Validity ended at'));
        $grid->column('status', __('Status'));
        $grid->column('inviter_id', __('Inviter id'));
        $grid->column('payed_at', __('Payed at'));
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
        $show = new Show(MemberRechargeOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        $show->field('balance', __('Balance'));
        $show->field('user_id', __('User id'));
        $show->field('member_recharge_activity_id', __('Member recharge activity id'));
        $show->field('member_recharge_activity_snapshot', __('Member recharge activity snapshot'));
        $show->field('member_level_id', __('Member level id'));
        $show->field('member_level_snapshot', __('Member level snapshot'));
        $show->field('validity_started_at', __('Validity started at'));
        $show->field('validity_ended_at', __('Validity ended at'));
        $show->field('status', __('Status'));
        $show->field('inviter_id', __('Inviter id'));
        $show->field('payed_at', __('Payed at'));
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
        $form = new Form(new MemberRechargeOrder);

        $form->text('order_number', __('Order number'));
        $form->number('balance', __('Balance'));
        $form->number('user_id', __('User id'));
        $form->number('member_recharge_activity_id', __('Member recharge activity id'));
        $form->text('member_recharge_activity_snapshot', __('Member recharge activity snapshot'));
        $form->number('member_level_id', __('Member level id'));
        $form->text('member_level_snapshot', __('Member level snapshot'));
        $form->datetime('validity_started_at', __('Validity started at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('validity_ended_at', __('Validity ended at'))->default(date('Y-m-d H:i:s'));
        $form->switch('status', __('Status'));
        $form->number('inviter_id', __('Inviter id'));
        $form->datetime('payed_at', __('Payed at'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
