<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\DefaultBatchTool;
use App\Models\CofferWithdrawal;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

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

        $grid->header(function ($query) {
            return '注意：暂不支持自动提现，请审核通过后记得进行微信或支付宝打款';
        });

        $grid->column('id', __('Id'));
//        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
        $grid->column('coffer', '用户当前金库(已结算/待结算)')->display(function ($item) {
            if (empty($this->owner->coffer)) return '';
            return strval($this->owner->coffer->balance * 0.01).'/'.strval($this->owner->coffer->unsettle_balance * 0.01);
        });
        $grid->column('balance', __('Balance'))->currency();
        $grid->column('status', __('Status'))->using(CofferWithdrawal::status_text)->filter(CofferWithdrawal::status_text);
        $grid->column('ip', __('Ip'))->ip();
        $grid->column('checked_at', __('Checked at'));
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
                $batch->add('批量通过', new DefaultBatchTool('batch/pass'));
                $batch->add('批量不通过', new DefaultBatchTool('batch/reject'));
            });
        });


        return $grid;
    }

    /**
     * 批操作
     * @param Request $request
     * @param string $handle 确认通过-pass,确认拒绝reject
     * @return \Illuminate\Http\JsonResponse
     * @author klinson <klinson@163.com>
     */
    public function batch(Request $request, $handle)
    {
        $orders = CofferWithdrawal::whereIn('id', $request->ids)->get();

        $info = [];
        $error_count = 0;
        foreach ($orders as $order) {
            if ($order->$handle()) {
                $info[] = "No.{$order->id}: 处理成功";
            } else {
                $error_count++;
                $info[] = "No.{$order->id}：处理失败";
            }
        }

        return show_batch_result($error_count, $info);
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
//        $show->field('order_number', __('Order number'));
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
