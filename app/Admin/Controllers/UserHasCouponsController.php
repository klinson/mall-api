<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use App\Models\User;
use App\Models\UserHasCoupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserHasCouponsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户拥有优惠券管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserHasCoupon);

        $grid->column('id', __('Id'));
        grid_display_relation($grid, 'owner', 'nickname');
        grid_display_relation($grid, 'coupon');
        $grid->column('coupon_snapshot', __('Coupon snapshot'))->display(function ($item) {
            return $item['title'];
        });
        $grid->column('discount_money', __('Discount money'))->currency();
        grid_has_enabled($grid);
        $grid->column('status', __('Status'))->using(UserHasCoupon::status_text);
        $grid->column('used_at', __('Used at'));
        $grid->column('description', __('Description'));
        $grid->column('created_at', __('Created at'));

        $grid->disableCreateButton();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('user_id', __('User id'))->select(User::all()->pluck('nickname', 'id'));
            $filter->equal('coupon_id', __('Coupon id'))->select(Coupon::all()->pluck('title', 'id'));

            $filter->like('description', __('Description'));

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
        $show = new Show(UserHasCoupon::findOrFail($id));

        $show->field('id', __('Id'));
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'coupon');
        $show->field('coupon_snapshot', __('Coupon snapshot'))->unescape()->array2json();
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('status', __('Status'))->using(UserHasCoupon::status_text);
        $show->field('discount_money', __('Discount money'))->currency();
        $show->field('used_at', __('Used at'));
        $show->field('description', __('Description'));
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
        $form = new Form(new UserHasCoupon);

        $form->number('user_id', __('User id'));
        $form->number('coupon_id', __('Coupon id'));
        $form->text('coupon_snapshot', __('Coupon snapshot'));
        $form->number('discount_money', __('Discount money'));
        $form->switch('has_enabled', __('Has enabled'));
        $form->switch('status', __('Status'));
        $form->datetime('used_at', __('Used at'))->default(date('Y-m-d H:i:s'));
        $form->text('description', __('Description'));

        return $form;
    }
}
