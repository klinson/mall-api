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

        $grid->model()->sort();

        $grid->column('id', __('Id'));
        $grid->column('sort', __('Sort'))->editable()->sortable()->help('1-999，越大越靠前');
        $grid->column('title', __('Title'));
        $grid->column('start_price', __('Start price'))->currency();
        $grid->column('face_value', __('Face value'))->display(function () {
            return $this->face_value_text;
        });
        $grid->column('type', __('Type'))->using(Coupon::type_text);
        $grid->column('limit', __('Coupon limit'))->help('每人限制领取数量');
        $grid->column('quantity', __('Quantity'));
        $grid->column('all_quantity', __('All quantity'));
        grid_has_enabled($grid);
        $grid->column('draw_started_at', __('Draw started at'));
        $grid->column('draw_ended_at', __('Draw ended at'));
        $grid->column('valid_started_at', __('Valid started at'));
        $grid->column('valid_ended_at', __('Valid ended at'));
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
        $show->field('start_price', __('Start price'));
        $show->field('face_value', __('Face value'))->as(function () {
            return $this->face_value_text;
        });
        $show->field('type', __('Type'))->using(Coupon::type_text);
        $show->field('limit', __('Coupon limit'));
        $show->field('quantity', __('Quantity'));
        $show->field('all_quantity', __('All quantity'));
        $show->field('sort', __('Sort'));
        $show->field('draw_started_at', __('Draw started at'));
        $show->field('draw_ended_at', __('Draw ended at'));
        $show->field('valid_started_at', __('Valid started at'));
        $show->field('valid_ended_at', __('Valid ended at'));
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
        $form->number('face_value', __('Face value'))->required()->help('依据类型：<br>1. 满减券：8.88=>优惠8.88元，（单位: 元）<br>2. 折扣券：8.8=>打8.8折，（最大值: 10=>原价，最小值：0.1=>0.1折)')->with(function ($value, $field) {
            if ($this && ! is_null($value) && $value == $this->face_value) {
                if ($this->type == 1) {
                    $value = strval($value * 0.1);
                } elseif ($this->type == 2) {
                    $value = strval($value * 0.01);
                }
            }
            // 必须这一步才能生效
            $field->attribute('value', $value);
            return $value;
        });

        $form->number('quantity', __('Quantity'))->min(0)->required();
        $form->number('all_quantity', __('All quantity'))->min(0)->required();
        $form->number('limit', __('Coupon limit'))->default(1)->min(0)->help('每人限制领取数量，0则不限制')->required();

        $form->datetime('draw_started_at', __('Draw started at'));
        $form->datetime('draw_ended_at', __('Draw ended at'));
        $form->datetime('valid_started_at', __('Valid started at'));
        $form->datetime('valid_ended_at', __('Valid ended at'));

        form_sort($form);

        $form->switch('has_enabled', __('Has enabled'))->default(1)->required();

        $form->saving(function ($form) {
            if ($form->type == 1) {
                $form->face_value = intval(strval($form->face_value * 10));
            } elseif ($form->type == 2) {
                $form->face_value = intval(strval($form->face_value * 100));
            }
        });

        return $form;
    }
}
