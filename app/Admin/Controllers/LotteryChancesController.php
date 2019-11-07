<?php

namespace App\Admin\Controllers;

use App\Models\LotteryChance;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LotteryChancesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '抽奖机会管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LotteryChance);

        $grid->column('id', __('Id'));
        grid_display_relation($grid, 'owner', 'nickname');
        $grid->column('used_at', __('Used at'));
        $grid->column('type', __('Type'))->using(LotteryChance::DESCRIPTIONS)->filter(LotteryChance::DESCRIPTIONS);;
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
        $show = new Show(LotteryChance::findOrFail($id));

        $show->field('id', __('Id'));
        show_display_relation($show, 'owner', 'nickname');
        $show->field('used_at', __('Used at'));
        $show->field('type', __('Type'))->using(LotteryChance::DESCRIPTIONS);
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
        $form = new Form(new LotteryChance);

        return $form;
    }
}
