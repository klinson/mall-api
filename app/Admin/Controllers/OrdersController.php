<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\AjaxWithFormButton;
use App\Admin\Extensions\Actions\GetButton;
use App\Admin\Extensions\Exporters\OrderExporter;
use App\Admin\Extensions\Tools\DefaultBatchTool;
use App\Models\Address;
use App\Models\AdminUser;
use App\Models\CofferLog;
use App\Models\Express;
use App\Models\Order;
use App\Models\RefundOrder;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->model()->with(['orderGoods'])->recent();

        $grid->column('id', __('Id'));
        $grid->column('order_number', __('Order number'))->expand(function () {
            $goods = $this->orderGoods->map(function ($item) {
                return [
                    'goods_title' => $item->snapshot['goods']['title'],
                    'goods_specification_title' => $item->snapshot['title'],
                    'price' => $item->price * 0.01 . ' 元',
                    'quantity' => $item->quantity,
                    'is_refund' => RefundOrder::status_text[$item->refund_status]
                ];
            })->toArray();

            return new Table([
                '商品名称',
                '商品规格',
                '单价',
                '数量',
                '退款？',
            ], $goods);
        });
        grid_display_relation($grid, 'user', 'nickname');

        $grid->column('coupon', __('Coupon id'))->display(function () {
            if (empty($this->coupon)) {
                return '';
            }
            return "<a target='_blank' href='/admin/userHasCoupons/{$this->coupon->id}'>{$this->coupon->coupon_snapshot['title']}</a>";
        });

        $grid->column('all_price', __('All price'))->currency()->sortable();
        $grid->column('goods_price', __('Goods price'))->currency()->sortable();
        $grid->column('member_discount_price', __('Member discount Price'))->currency()->sortable();
        $grid->column('coupon_price', __('Coupon price'))->currency()->sortable();
//        $grid->column('allow_coupon_price', __('Allow coupon price'))->currency()->sortable();
        $grid->column('used_integral', __('Used integral'))->display(function ($item) {
            if (empty($item)) return 0;
            else return "￥".($this->integral_price*0.01)." ({$this->used_integral}分)";
        });
        $grid->column('freight_price', __('Freight price'))->currency()->sortable();;
        $grid->column('real_price', __('Real price'))->currency()->sortable();
        $grid->column('pay_mode', '支付方式')->display(function () {
            return $this->used_balance ? '钱包' : '微信';
        });
        $grid->column('delivery_type', __('Delivery type'))->using(Order::delivery_type_map);
        $grid->column('delivery_snapshot', __('Delivery snapshot'))->display(function ($item) {
            if ($this->delivery_type == Address::class) {
                return "{$item['name']}|{$item['mobile']}<br>".($item['city_name'] ?? '')."-{$item['address']}";
            } else {
                return $item['title'];
            }
        });

        $grid->column('remarks', __('Remarks'));

        $grid->column('status', __('Status'))->using(Order::status_text)->filter(Order::status_text);
        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');
        $grid->column('payed_at', __('Payed at'))->sortable()->filter('range', 'datetime');
        $grid->column('expressed_at', __('Expressed at'))->sortable()->filter('range', 'datetime');
        $grid->column('confirmed_at', __('Confirmed at'))->sortable()->filter('range', 'datetime');

        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            if ($this->row->status === 2) {
                $actions->append(new AjaxWithFormButton(
                    $actions->getResource() . '/' . $actions->getKey() . '/express',
                    '发货',
                    [
                        'title' => '发货',
                        'footer' => '上门自提或其他非快递配送，可选择无需物流',
                    ],
                    [
                        [
                            'title' => '物流公司',
                            'name' => 'express_id',
                            'input' => 'select',
                            'inputOptions' => array_merge(['无需物流'], Express::all(['id', 'name'])->pluck('name', 'id')->toArray()),
                            'inputValue' => config('system.express_company_id', 1)
                        ],
                        [
                            'title' => '物流单号',
                            'name' => 'express_number',
                            'input' => 'text',
                            'text' => '请输入',
                            'inputPlaceholder' => '无需物流可不填'
                        ]
                    ]
                ));
            }
            if ($this->row->status > 2 && $this->row->delivery_type == Address::class) {
                $actions->append(new GetButton(
                    $actions->getResource() . '/' . $actions->getKey() . '/logistics',
                    '物流查询'
                ));
            }
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $batch) {
//                $batch->disableDelete();
                $batch->add('批量取消订单', new DefaultBatchTool('cancel'));
                // 暂时未对接快递，无法自动批量发货
//                $batch->add('批量确认发货', new DefaultBatchTool('batch/express'));
                $batch->add('批量确认到货', new DefaultBatchTool('batch/receive'));
            });
        });

        $grid->filter(function(Grid\Filter $filter){
            $filter->like('order_number', __('Order number'));
            $filter->like('express_number', __('Express number'));
            $filter->equal('delivery_type', __('Delivery type'))->select(Order::delivery_type_map);
        });

        $grid->exporter(new OrderExporter());

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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        show_display_relation($show, 'user', 'nickname');
        $show->field('coupon', __('Coupon id'))->unescape()->as(function () {
            if (empty($this->coupon)) {
                return '';
            }
            return "<a target='_blank' href='/admin/userHasCoupons/{$this->coupon->id}'>{$this->coupon->coupon_snapshot['title']}</a>";
        });

        $show->field('all_price', __('All price'))->currency();
        $show->field('goods_price', __('Goods price'))->currency();
        $show->field('member_discount_price', __('Member discount Price'))->currency();
        $show->field('allow_coupon_price', __('Allow coupon price'))->currency();
        $show->field('coupon_price', __('Coupon price'))->currency();
        $show->field('used_integral', __('Used integral'))->as(function ($item) {
            if (empty($item)) return 0;
            else return "￥".($this->integral_price*0.01)." ({$this->used_integral}分)";
        });
        $show->field('freight_price', __('Freight price'))->currency();;
        $show->field('real_price', __('Real price'))->currency();
        $show->field('pay_mode', '支付方式')->as(function () {
            return $this->used_balance ? '钱包' : '微信';
        });
        $show->field('delivery_type', __('Delivery type'))->using(Order::delivery_type_map);
        $show->field('delivery_snapshot', __('Delivery snapshot'))->unescape()->as(function ($item) {
            if ($this->delivery_type == Address::class) {
                return "{$item['name']}|{$item['mobile']}<br>".($item['city_name'] ?? '')."-{$item['address']}";
            } else {
                return $item['title'];
            }
        });
        $show->field('confirmUser', __('Confirm user'))->as(function ($item) {
            switch (get_class($item)) {
                case AdminUser::class:
                    $name = "【管理员】".$item->name;
                    break;
                case User::class:
                    if ($this->user_id == $this->confirm_user_id) {
                        $name = "【用户】";
                    } else {
                        $name = "【职员】";
                    }
                    $name .= $item->nickname . ' | ' . $item->mobile;
                    break;
                default:
                    $name = '';
                    break;
            }
            return $name;
        });
        $show->field('remarks', __('Remarks'));

        $show->field('status', __('Status'))->using(Order::status_text)->filter(Order::status_text);
        $show->field('created_at', __('Created at'));
        $show->field('payed_at', __('Payed at'));
        $show->field('expressed_at', __('Expressed at'));
        $show->field('confirmed_at', __('Confirmed at'));
        $show->field('updated_at', __('Updated at'));

        $show->field('cancel_order_number', __('Cancel order number'));
        $show->field('cancel_reason', __('Cancel reason'));
        $show->field('goods_count', __('Goods count'));
        $show->field('goods_weight', __('Goods weight'));


        $show->orderGoods('订单商品', function (Grid $grid) {
            $grid->column('id', __('Id'));
            grid_display_relation($grid, 'goods');
            grid_display_relation($grid, 'specification');
            grid_display_relation($grid, 'marketing');
            $grid->column('price', '原价')->currency();
            $grid->column('quantity', __('Quantity'));
            $grid->column('real_price', '实际支付')->currency();
            $grid->column('refund_status', __('Refund status'))->using(RefundOrder::status_text);
            grid_display_relation($grid, 'inviter', 'nickname');
            grid_display_relation($grid, 'refundOrder', 'order_number');

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });

        $show->refunds('售后订单', function (Grid $grid) {
            $grid->setResource('/admin/refundOrders');

            $grid->column('id', __('Id'));
            $grid->column('order_number', __('Order number'));
            $grid->column('orderGoods', '退款商品')->display(function ($item) {
                return $this->orderGoods->toString();
            });
            $grid->column('quantity', __('Quantity'));
            $grid->column('price', __('Price'))->currency();
            $grid->column('real_price', '实际应退')->currency();
            $grid->column('freight_price', __('Freight price'))->currency();
            $grid->column('status', __('Status'))->using(RefundOrder::status_text);
            $grid->column('reason_text', __('Reason text'));
            $grid->column('reason_images', __('Reason Images'))->image();
            $grid->column('expressed_at', __('Expressed at'));
            $grid->column('created_at', __('Created at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
                $actions->disableDelete();
            });
        });

        $show->cofferLogs('金库结算日志', function (Grid $grid) {
            $grid->column('id', __('Id'));
            grid_display_relation($grid, 'owner', 'nickname');
            $grid->column('balance', __('Balance'))->currency();
            $grid->column('type', __('Type'))->using(CofferLog::type_text);
            $grid->column('description', __('Description'));
            $grid->column('ip', __('Ip'))->ip();
            $grid->column('created_at', __('Created at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableActions();
        });

        return $show;
    }

    public function logistics(Order $order, Content $content)
    {
        try {
            $res = $order->getLogistics();
            $content->title($this->title);

            $header = [
                'id', '内容', '时间'
            ];
            $body = [];
            foreach ($res['data'] as $key => $data) {
                $body[] = [
                    $key+1,
                    $data['context'],
                    $data['time'],
                ];
            }
            $box = new Box("订单【{$order->order_number}】【{$res['com_name']}：{$order->express_number}】物流信息（最终状态：".Order::express_status_text[$res['state']]."）", new Table($header, $body));
            $content->body($box);

            return $content;
        } catch (\Exception $exception) {
            admin_toastr($exception->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->display('id');

        return $form;
    }

    //批量操作 确认发货，确认收货
    public function batch(Request $request, $handle)
    {
        $orders = Order::whereIn('id', $request->ids)->get();
        $info = [];
        $error_count = 0;
        foreach ($orders as $order) {
            if ($order->$handle()) {
                $info[] = "No.{$order->id}：{$order->order_number} 处理成功";
            } else {
                $error_count++;
                $info[] = "No.{$order->id}：{$order->order_number} 处理失败";
            }
        }

        return show_batch_result($error_count, $info);
    }

    public function express(Order $order, Request $request)
    {
        if ($order->status !== 2) {
            $data = [
                'status'  => false,
                'message' => '订单状态异常',
            ];
            return response()->json($data);
        }

        if (! empty($request->express_id)) {
            if (empty($request->express_number)) {
                $data = [
                    'status'  => false,
                    'message' => '请输入快递单号',
                ];
                return response()->json($data);
            }
        }

        $order->expressing($request->express_number, $request->express_id ?: 0);

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }

    // 取消订单
    public function cancel(Request $request)
    {
        $orders = Order::whereIn('status', [1, 2, 3, 4])
            ->whereIn('id', $request->ids)
            ->get();
        if (! $orders->isEmpty()) {
            $info = [];
            $code = 0;
            foreach ($orders as $order) {
                try {
                    $order->cancel();
                    $info[] = "No.{$order->id}：{$order->order_number} 取消成功";
                } catch (\Exception $exception) {
                    $code = 1;
                    $info[] = "No.{$order->id}：{$order->order_number} 取消失败，{$exception->getMessage()}";
                }
            }
            if (! $code) {
                admin_success('处理成功', implode("<br/>", $info));
            } else {
                admin_warning('处理完成，存在失败，请勿频繁重试', implode("<br/>", $info));
            }
        }

        $data = [
            'status'  => true,
            'message' => '操作成功',
        ];
        return response()->json($data);
    }
}
