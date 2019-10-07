<?php

namespace App\Admin\Controllers;

use App\Models\FreightTemplate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FreightTemplatesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '运费模板';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FreightTemplate);

        $grid->model()->sort();

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('basic_cost', __('Basic cost'));
        $grid->column('pinkage_type', __('Pinkage type'))->using(FreightTemplate::pinkage_types);
        $grid->column('pinkage_number', __('Pinkage number'));
        $grid->column('continued_cost', __('Continued cost'));
        $grid->column('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $grid->column('sort', __('Sort'));

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
        $show = new Show(FreightTemplate::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('basic_cost', __('Basic cost'));
        $show->field('pinkage_type', __('Pinkage type'))->using(FreightTemplate::pinkage_types);
        $show->field('pinkage_number', __('Pinkage number'));
        $show->field('continued_cost', __('Continued cost'));
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('sort', __('Sort'));
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
        $form = new Form(new FreightTemplate);

        $form->text('title', __('Title'));
        $form->currency('basic_cost', __('Basic cost'))->default(1000);
        $form->select('pinkage_type', __('Pinkage type'))->options(FreightTemplate::pinkage_types);
        $form->number('pinkage_number', __('Pinkage number'))->default(0);
        $form->currency('continued_cost', __('Continued cost'))->default(1000);
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->number('sort', __('Sort'))->default(0);
        $form->areaCheckbox('addresses', __('Addresses'));

        return $form;
    }
}
