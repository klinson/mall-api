<?php

namespace App\Admin\Controllers;

use App\Models\CofferWithdrawal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CofferWithdrawalsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '提现申请管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CofferWithdrawal);

        $grid->model()->recent();

        admin_warning('注意', '暂不支持自动提现，请审核通过后记得进行微信或支付宝打款');

        $grid->header(function ($query) {
            return '注意：暂不支持自动提现，请审核通过后记得进行微信或支付宝打款';
        });

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
        $grid->column('balance', __('Balance'))->currency();
        $grid->column('status', __('Status'))->using(CofferWithdrawal::status_text)->filter(CofferWithdrawal::status_text);
        $grid->column('ip', __('Ip'))->ip();
        $grid->column('checked_at', __('Checked at'));
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
        $show = new Show(CofferWithdrawal::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'owner', 'nickname');
        $show->field('balance', __('Balance'))->currency();
        $show->field('status', __('Status'))->using(CofferWithdrawal::status_text);
        $show->field('ip', __('Ip'))->ip();
        $show->field('checked_at', __('Checked at'));
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
        $form = new Form(new CofferWithdrawal);


        return $form;
    }
}
