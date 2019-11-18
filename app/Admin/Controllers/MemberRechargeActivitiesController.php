<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use App\Models\MemberLevel;
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
    protected $title = '会员充值活动管理';

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
        $grid->column('thumbnail', __('Thumbnail'))->image();
        grid_display_relation($grid, 'memberLevel');

        $grid->column('validity_times', __('Validity times'))->display(function ($item) {
            return $this->real_validity_time;
        });
        $grid->column('recharge_threshold', __('Recharge threshold'))->currency();
        $grid->column('level', __('Level'));
        $grid->column('invite_award', __('Invite award'))->display(function ($item) {
            return $this->invite_real_award;
        })->currency();
        grid_has_enabled($grid);

        $grid->column('created_at', __('Created at'));

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
        $show->field('thumbnail', __('Thumbnail'))->image();
        show_display_relation($show, 'memberLevel');

        $show->field('validity_times', __('Validity times'))->as(function ($item) {
            return $this->real_validity_time;
        });
        $show->field('recharge_threshold', __('Recharge threshold'))->currency();
        $show->field('level', __('Level'));
        $show->field('invite_award', __('Invite award'))->as(function ($item) {
            return $this->invite_real_award;
        })->currency();
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
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
        $form = new Form(new MemberRechargeActivity);

        $form->text('title', __('Title'))->required();
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        MemberLevel::form_display_select($form, 'member_level_id')->required();
        $form->select('validity_type', __('Validity type'))->options(MemberRechargeActivity::validity_type_text)->required();
        $form->number('validity_times', __('Validity times'))->default(0)->required();
        $form->currency('recharge_threshold', __('Recharge threshold'))->required();
        $form->number('level', __('Level'))->default(0)->required();
        $form->select('invite_award_mode', __('Invite award mode'))->options(MemberRechargeActivity::invite_award_mode_text)->required();
        $form->number('invite_award', __('Invite award'))->default(0)->required()->help('依据邀请利润模式，固定佣金则288=>2.88元(单位分)，比例佣金则288=>门槛金*0.288佣金');
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        $form->hasMany('hasCoupons', '赠送优惠券', function($form) {
            Coupon::form_display_select($form, 'coupon_id', 'title', __('Coupon id'))->required();
            $form->number('count', __('Count'))->default(1)->rules(['required', 'integer', 'min:0']);
        });

        return $form;
    }
}
