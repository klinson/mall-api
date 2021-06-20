<?php

namespace App\Admin\Controllers;

use App\Models\MemberLevel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MemberLevelsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '会员等级管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MemberLevel);

        $grid->column('id', __('Id'));
        $grid->column('level', __('Level'));
        $grid->column('title', __('Title'));
        $grid->column('logo', __('Logo'))->image();
        $grid->column('score', __('Member level score'));
        $grid->column('discount', __('Discount'))->display(function ($item) {
            return $item * 0.1 . '折';
        });
        $grid->column('is_fee_freight', __('Is fee freight'))->using(YN2TEXT);

        grid_has_enabled($grid);

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(MemberLevel::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('level', __('Level'));
        $show->field('title', __('Title'));
        $show->field('logo', __('Logo'))->image();
        $show->field('score', __('Member level score'));
        $show->field('discount', __('Discount'))->as(function ($item) {
            return $item * 0.1 . '折';
        });
        $show->field('is_fee_freight', __('Is fee freight'))->using(YN2TEXT);
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
        $form = new Form(new MemberLevel);

        $form->text('title', __('Title'))->required();
        $form->number('level', __('Level'))->default(0)->required()->help('等级约大代表此会员越高级');
        $form->image('logo', __('Logo'))->uniqueName();
        $form->number('score', __('Member level score'))->default(0)->required()->min(0);
        $form->number('discount', __('Discount'))->default(10)->max(10)->min(0.1)->required()->help('折扣，8.8=>8.8折，（最大值: 10=>原价，最小值：0.1=>0.1折)')->with(function ($value, $field) {
            // 编辑数据的时候需要初始化处理
            $column_name = $field->column();
            $old_data = old($column_name);
            // 提交失败的数据不需要处理
            if (! $old_data) {
                $new_value = false;
                if ($this && ! is_null($value) && $value == $this->$column_name) {
                    $new_value = strval($value * 0.1);
                }
                if ($new_value !== false) {
                    // 必须这一步才能生效
                    $value = $new_value;
                    $field->attribute('value', $value);
                }
            }

            return $value;
        });

        $form->switch('is_fee_freight', __('Is fee freight'))->default(0);
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        $form->saving(function ($form) {
            $form->discount = intval(strval($form->discount * 10));
        });

        return $form;
    }
}
