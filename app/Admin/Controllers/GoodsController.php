<?php

namespace App\Admin\Controllers;

use App\Models\Goods;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Goods';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category id'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'));
        $grid->column('images', __('Images'));
        $grid->column('detail', __('Detail'));
        $grid->column('max_price', __('Max price'));
        $grid->column('min_price', __('Min price'));
        $grid->column('has_enabled', __('Has enabled'));
        $grid->column('has_recommended', __('Has recommended'));
        $grid->column('sort', __('Sort'));
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
        $show = new Show(Goods::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('title', __('Title'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('images', __('Images'));
        $show->field('detail', __('Detail'));
        $show->field('max_price', __('Max price'));
        $show->field('min_price', __('Min price'));
        $show->field('has_enabled', __('Has enabled'));
        $show->field('has_recommended', __('Has recommended'));
        $show->field('sort', __('Sort'));
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
        $form = new Form(new Goods);

        $form->number('category_id', __('Category id'));
        $form->text('title', __('Title'));
        $form->text('thumbnail', __('Thumbnail'));
        $form->text('images', __('Images'));
        $form->textarea('detail', __('Detail'));
        $form->number('max_price', __('Max price'));
        $form->number('min_price', __('Min price'));
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->switch('has_recommended', __('Has recommended'))->default(1);
        $form->switch('sort', __('Sort'));

        return $form;
    }
}
