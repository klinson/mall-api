<?php

namespace App\Admin\Controllers;

use App\Models\WalletActivity;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WalletActivitiesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '充值活动';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WalletActivity);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('threshold', __('Threshold'))->currency();;
        $grid->column('present', __('Present'))->currency();
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
        $show = new Show(WalletActivity::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('threshold', __('Threshold'))->currency();;
        $show->field('present', __('Present'))->currency();;
        $show->field('has_enabled', __('Has enabled'))->using(YN2TEXT);
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
        $form = new Form(new WalletActivity);

        $form->text('title', __('Title'))->required();
        $form->currency('threshold', __('Threshold'))->required();
        $form->currency('present', __('Present'))->required();
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
