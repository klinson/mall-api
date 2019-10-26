<?php

namespace App\Admin\Controllers;

use App\Models\AgencyConfig;
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
        $grid->model()->with(['agency', 'wallet', 'coffer'])->orderBy('created_at', 'desc');

        $grid->column('id', __('Id'));
        $grid->column('nickname', __('Nickname'));
        $grid->column('avatar', __('Avatar'))->image();
        grid_display_relation($grid, 'agency', 'title');
        $grid->column('sex', __('Sex'))->using(User::SEX2TEXT)->filter(User::SEX2TEXT);;
        $grid->column('mobile', __('Mobile'));
        $grid->column('wallet', __('Wallet'))->display(function ($item) {
            return strval($this->wallet->balance * 0.01);
        });
        $grid->column('coffer', '金库(已结算/待结算)')->display(function ($item) {
            if (empty($this->coffer)) return '';
            return strval($this->coffer->balance * 0.01).'/'.strval($this->coffer->unsettle_balance * 0.01);
        });

        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');

        $grid->disableCreateButton();

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('nickname', __('Nickname'));
            $filter->like('mobile', __('Mobile'));
            $filter->like('wxapp_openid', __('Wxapp openid'));
            $filter->equal('agency_id', __('Agency id'))->select(AgencyConfig::all(['id', 'title'])->pluck('title', 'id')->toArray());
        });

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
        $show->field('avatar', __('Avatar'))->image();
        $show->field('nickname', __('Nickname'));
        $show->field('wxapp_openid', __('Wxapp openid'));
        $show->field('sex', __('Sex'))->using(User::SEX2TEXT);
        $show->field('mobile', __('Mobile'));
        $show->field('wxapp_userinfo', __('Wxapp userinfo'))->json();
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
