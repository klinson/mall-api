<?php

namespace App\Admin\Controllers;

use App\Models\WalletLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WalletLogsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '钱包日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WalletLog);
        $grid->model()->recent();

        $grid->column('id', __('Id'));
        grid_display_relation($grid, 'owner', 'nickname');
        $grid->column('data_id', __('Data id'));
        $grid->column('data_type', __('Data type'));
        $grid->column('balance', __('Balance'))->currency();
        $grid->column('type', __('Type'))->using(WalletLog::type_text);
        $grid->column('description', __('Description'));
        $grid->column('ip', __('Ip'))->ip();
        $grid->column('created_at', __('Created at'));

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableColumnSelector();
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
        $show = new Show(WalletLog::findOrFail($id));

        $show->field('id', __('Id'));
        show_display_relation($show, 'owner', 'nickname');
        $show->field('data_id', __('Data id'));
        $show->field('data_type', __('Data type'));
        $show->field('balance', __('Balance'))->currency();
        $show->field('type', __('Type'))->using(WalletLog::type_text);
        $show->field('description', __('Description'));
        $show->field('ip', __('Ip'))->ip();
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WalletLog);

        $form->number('user_id', __('User id'));
        $form->number('data_id', __('Data id'));
        $form->text('data_type', __('Data type'));
        $form->number('balance', __('Balance'));
        $form->switch('type', __('Type'));
        $form->text('description', __('Description'));
        $form->number('ip', __('Ip'));

        return $form;
    }
}
