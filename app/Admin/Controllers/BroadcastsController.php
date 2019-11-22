<?php

namespace App\Admin\Controllers;

use App\Models\Broadcast;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BroadcastsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '广播管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Broadcast);

        $grid->column('id', __('Id'));
        $grid->column('content', __('Content'));
        grid_has_enabled($grid);
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
        $show = new Show(Broadcast::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('content', __('Content'));
        $show->field('has_enabled', __('Has enabled'));
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
        $form = new Form(new Broadcast);

        $form->textarea('content', __('Content'));
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
