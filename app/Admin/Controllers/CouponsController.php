<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '优惠券管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coupon);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('start_price', __('Start price'))->currency();
        $grid->column('face_value', __('Face value'))->display(function () {
            return $this->face_value_text;
        });
        $grid->column('type', __('Type'))->using(Coupon::type_text);
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
        $show = new Show(Coupon::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('start_price', __('Start price'))->currency();
        $show->field('face_value', __('Face value'))->as(function () {
            return $this->face_value_text;
        });
        $show->field('type', __('Type'))->using(Coupon::type_text);
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
        $form = new Form(new Coupon);

        $form->text('title', __('Title'))->required();
        $form->currency('start_price', __('Start price'))->required();
        $form->select('type', __('Type'))->options(Coupon::type_text)->required();
        $form->number('face_value', __('Face value'))->required()->help('依据类型，满减券折（单位: 分），则288=>2.88元，折扣券则88=>打8.8折（最大值: 100=>原价)');
        $form->switch('has_enabled', __('Has enabled'))->default(1)->required();

        return $form;
    }
}
