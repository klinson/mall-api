<?php

namespace App\Admin\Controllers;

use App\Models\Address;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AddressesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Address';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Address);

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('name', __('Name'));
        $grid->column('mobile', __('Mobile'));
        $grid->column('province_id', __('Province id'));
        $grid->column('city_id', __('City id'));
        $grid->column('district_id', __('District id'));
        $grid->column('address', __('Address'));
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
        $show = new Show(Address::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('name', __('Name'));
        $show->field('mobile', __('Mobile'));
        $show->field('province_id', __('Province id'));
        $show->field('city_id', __('City id'));
        $show->field('district_id', __('District id'));
        $show->field('address', __('Address'));
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
        $form = new Form(new Address);

        $form->number('user_id', __('User id'));
        $form->text('name', __('Name'));
        $form->mobile('mobile', __('Mobile'));
        $form->number('province_id', __('Province id'));
        $form->number('city_id', __('City id'));
        $form->number('district_id', __('District id'));
        $form->text('address', __('Address'));

        return $form;
    }
}
