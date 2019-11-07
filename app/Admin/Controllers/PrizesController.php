<?php

namespace App\Admin\Controllers;

use App\Models\Prize;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PrizesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '奖品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Prize);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('origin_quantity', __('Origin quantity'));
        $grid->column('quantity', __('Quantity'));
        $grid->column('price', __('Price'))->currency();
        $grid->column('level', __('Level'));
        $grid->column('rate', __('Rate'));
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
        $show = new Show(Prize::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('origin_quantity', __('Origin quantity'));
        $show->field('quantity', __('Quantity'));
        $show->field('price', __('Price'));
        $show->field('level', __('Level'))->currency();
        $show->field('rate', __('Rate'));
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
        $form = new Form(new Prize);

        $form->text('title', __('Title'))->required();
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->number('origin_quantity', __('Origin quantity'))->default(1)->required();
        $form->number('quantity', __('Quantity'))->default(1)->required();
        $form->currency('price', __('Price'))->required();
        $form->number('level', __('Level'))->default(1)->required();
        $form->number('rate', __('Rate'))->required();
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
