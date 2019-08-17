<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Rules\CnMobile;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->column('id', __('Id'));
        $grid->column('wxapp_openid', __('Wxapp openid'));
        $grid->column('nickname', __('Nickname'));
        $grid->column('sex', __('Sex'))->using(User::SEX2TEXT);
        $grid->column('mobile', __('Mobile'));
        $grid->column('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('wxapp_openid', __('Wxapp openid'));
        $show->field('nickname', __('Nickname'));
        $show->field('sex', __('Sex'))->using(User::SEX2TEXT);
        $show->field('mobile', __('Mobile'));
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
        $form = new Form(new User);

        $form->text('wxapp_openid', __('Wxapp openid'))->required()->rules(['max:28']);
        $form->text('nickname', __('Nickname'))->required()->rules(['max:30']);
        $form->select('sex', __('Sex'))->options(User::SEX2TEXT)->required();
        $form->mobile('mobile', __('Mobile'))->required()->rules([new CnMobile()]);
        $form->switch('has_enabled', __('Has enabled'))->default(1);

        return $form;
    }
}
