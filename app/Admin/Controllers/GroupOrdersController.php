<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\AjaxButton;
use App\Models\GroupOrder;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GroupOrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '团购订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new GroupOrder);

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'));
        grid_display_relation($grid, 'owner', 'nickname');
        grid_display_relation($grid, 'admin', 'username');
        $grid->column('all_price', __('All price'))->currency()->sortable();;
        $grid->column('remarks', __('Remarks'));
        $grid->column('status', __('Status'))->using(GroupOrder::status_text)->filter(GroupOrder::status_text);
        $grid->column('payed_at', __('Payed at'));
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            if ($this->row->status == 1) {
                $actions->append(new AjaxButton($actions->getResource().'/'.$actions->getKey().'/pay', '确认支付', 'success'));
                $actions->append(new AjaxButton($actions->getResource().'/'.$actions->getKey().'/cancel', '取消订单', 'warning'));
            }
        });
        $grid->filter(function(Grid\Filter $filter){
            $filter->like('order_number', __('Order number'));
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
        $show = new Show(GroupOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'owner', 'nickname');
        show_display_relation($show, 'admin', 'name');
        $show->field('all_price', __('All price'))->currency();
        $show->field('remarks', __('Remarks'));
        $show->field('status', __('Status'))->using(GroupOrder::status_text);
        $show->field('payed_at', __('Payed at'));
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
        $form = new Form(new GroupOrder);

        $form->text('order_number', __('Order number'))->default(GroupOrder::generateOrderNumber())->readonly();
        User::form_display_select($form, 'user_id', 'nickname,mobile', '', false, 'nickname,mobile')->required();
        $form->currency('all_price', __('All price'))->required();
        $form->textarea('remarks', __('Remarks'));
//        $form->switch('status', __('Status'));
//        $form->datetime('payed_at', __('Payed at'))->default(date('Y-m-d H:i:s'));

        return $form;
    }

    public function pay($id)
    {
        $model = GroupOrder::findOrFail($id);
        if ($model->pay()) {
            $data = [
                'status'  => true,
                'message' => '操作成功',
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => '操作失败',
            ];
        }

        return response()->json($data);
    }

    public function cancel($id)
    {
        $model = GroupOrder::findOrFail($id);
        if ($model->cancel()) {
            $data = [
                'status'  => true,
                'message' => '操作成功',
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => '操作失败',
            ];
        }

        return response()->json($data);
    }
}
