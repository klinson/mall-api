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
    protected $title = 'App\Models\MemberLevel';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MemberLevel);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('logo', __('Logo'));
        $grid->column('discount', __('Discount'));
        $grid->column('has_enabled', __('Has enabled'));
        $grid->column('level', __('Level'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('is_fee_freight', __('Is fee freight'));

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
        $show->field('title', __('Title'));
        $show->field('logo', __('Logo'));
        $show->field('discount', __('Discount'));
        $show->field('has_enabled', __('Has enabled'));
        $show->field('level', __('Level'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('is_fee_freight', __('Is fee freight'));

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

        $form->text('title', __('Title'));
        $form->text('logo', __('Logo'));
        $form->number('discount', __('Discount'));
        $form->switch('has_enabled', __('Has enabled'));
        $form->switch('level', __('Level'));
        $form->number('is_fee_freight', __('Is fee freight'));

        return $form;
    }
}
