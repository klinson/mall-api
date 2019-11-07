<?php

namespace App\Admin\Controllers;

use App\Models\LotteryRecord;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LotteryRecordsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '中奖记录管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LotteryRecord);

        $grid->column('id', __('Id'));
        $grid->column('prize_snapshot', __('Prize id'))->display(function ($item) {
            return "{$item['title']}";
        });
        grid_display_relation($grid, 'owner', 'nickname');

        grid_display_relation($grid, 'express', 'name');
        $grid->column('express_number', __('Express number'));
        $grid->column('address_snapshot', __('Address'))->display(function ($item) {
            return "{$item['name']}|{$item['mobile']}<br>{$item['city_name']}-{$item['address']}";
        });
        $grid->column('expressed_at', __('Expressed at'));
        $grid->column('status', __('Status'))->using(LotteryRecord::status_text)->filter(LotteryRecord::status_text);
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
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
        $show = new Show(LotteryRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('prize_snapshot', __('Prize snapshot'))->as(function ($item) {
            return "{$item['title']}";
        });
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'express', 'name');

        $show->field('express_number', __('Express number'));
        $show->field('address_snapshot', __('Address snapshot'))->as(function ($item) {
            return "{$item['name']}|{$item['mobile']}<br>{$item['city_name']}-{$item['address']}";
        });
        $show->field('expressed_at', __('Expressed at'));
        $show->field('status', __('Status'))->using(LotteryRecord::status_text);
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
        $form = new Form(new LotteryRecord);

        return $form;
    }
}
