<?php

namespace App\Admin\Controllers;

use App\Models\MemberRechargeActivity;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MemberRechargeActivitiesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\MemberRechargeActivity';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MemberRechargeActivity);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'));
        $grid->column('member_level_id', __('Member level id'));
        $grid->column('validity_type', __('Validity type'));
        $grid->column('validity_times', __('Validity times'));
        $grid->column('recharge_threshold', __('Recharge threshold'));
        $grid->column('level', __('Level'));
        $grid->column('invite_award_mode', __('Invite award mode'));
        $grid->column('invite_award', __('Invite award'));
        $grid->column('has_enabled', __('Has enabled'));
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
        $show = new Show(MemberRechargeActivity::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('member_level_id', __('Member level id'));
        $show->field('validity_type', __('Validity type'));
        $show->field('validity_times', __('Validity times'));
        $show->field('recharge_threshold', __('Recharge threshold'));
        $show->field('level', __('Level'));
        $show->field('invite_award_mode', __('Invite award mode'));
        $show->field('invite_award', __('Invite award'));
        $show->field('has_enabled', __('Has enabled'));
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
        $form = new Form(new MemberRechargeActivity);

        $form->text('title', __('Title'));
        $form->text('thumbnail', __('Thumbnail'));
        $form->number('member_level_id', __('Member level id'));
        $form->switch('validity_type', __('Validity type'));
        $form->number('validity_times', __('Validity times'));
        $form->number('recharge_threshold', __('Recharge threshold'));
        $form->switch('level', __('Level'));
        $form->switch('invite_award_mode', __('Invite award mode'));
        $form->number('invite_award', __('Invite award'));
        $form->switch('has_enabled', __('Has enabled'));

        return $form;
    }
}
