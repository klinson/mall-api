<?php

namespace App\Admin\Controllers;

use App\Models\AgencyConfig;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AgencyConfigsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '代理等级管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AgencyConfig);
        $grid->model()->withCount('users');
        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('users_count', '代理人数');
        $grid->column('recharge_threshold', __('Recharge threshold'))->currency()->sortable();;
        $grid->column('direct_profit', '直推产品利润')->display(function () {
            return $this->direct_profit_show;
        });
        $grid->column('indirect_profit', '间推产品利润')->display(function () {
            return $this->indirect_profit_show;
        });
        $grid->column('direct_agency', '直推代理利润')->display(function () {
            return $this->direct_agency_show;
        });
        $grid->column('indirect_agency', '间推代理利润')->display(function () {
            return $this->indirect_agency_show;
        });
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
        $show = new Show(AgencyConfig::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('users_count', '代理人数')->as(function () {
            return $this->users()->count();
        });

        $show->field('recharge_threshold', __('Recharge threshold'))->currency();
        $show->field('direct_profit', '直推产品利润')->as(function () {
            return $this->direct_profit_show;
        });
        $show->field('indirect_profit', '间推产品利润')->as(function () {
            return $this->indirect_profit_show;
        });
        $show->field('direct_agency', '直推代理利润')->as(function () {
            return $this->direct_agency_show;
        });
        $show->field('indirect_agency', '间推代理利润')->as(function () {
            return $this->indirect_agency_show;
        });
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
        $form = new Form(new AgencyConfig);

        $form->text('title', __('Title'))->help('<strong>结算方式说明：</strong><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. 固定利润=>佣金金额（单位：分，例：1234=>￥12.34）<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. 比例提成=>佣金提成百分比（例：25=>25%，不设小数点）');
        $form->currency('recharge_threshold', __('Recharge threshold'))->rules(['required', 'min:1', 'numeric']);

        $form->select('direct_profit_mode', __('Direct profit mode'))->options(AgencyConfig::mode_text);
        $form->number('direct_profit', __('Direct profit'));
        $form->select('indirect_profit_mode', __('Indirect profit mode'))->options(AgencyConfig::mode_text);
        $form->number('indirect_profit', __('Indirect profit'));
        $form->select('direct_agency_mode', __('Direct agency mode'))->options(AgencyConfig::mode_text);
        $form->number('direct_agency', __('Direct agency'));
        $form->select('indirect_agency_mode', __('Indirect agency mode'))->options(AgencyConfig::mode_text);
        $form->number('indirect_agency', __('Indirect agency'));

        return $form;
    }
}
