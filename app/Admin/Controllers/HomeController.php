<?php

namespace App\Admin\Controllers;

use App\Models\CofferWithdrawal;
use App\Models\LotteryRecord;
use App\Models\Order;
use App\Models\RefundOrder;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    protected $pageHeader = 'Dashboard';

    public function index(Content $content)
    {
        $this->_setPageDefault($content);
        $content->row(view('admin.dashboard.title'));
        $list = [
            [
                'title' => '工作台',
                'width' => 4,
                'data' => [
                    [
                        'title' => '订单待发货',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders?status[]=2',
                        'info' => Order::where('status', 2)->count(),
                    ],
                    [
                        'title' => '售后待审核',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/refundOrders?status[]=1',
                        'info' => RefundOrder::where('status', 1)->count(),
                    ],
                    [
                        'title' => '售后待确认',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/refundOrders?status[]=3',
                        'info' => RefundOrder::where('status', 3)->count(),
                    ],
                    [
                        'title' => '中奖待发货',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/lotteryRecords',
                        'info' => LotteryRecord::where('status', 1)->count(),
                    ],
                    [
                        'title' => '提现待处理',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/cofferWithdrawals',
                        'info' => CofferWithdrawal::where('status', 1)->count(),
                    ],
                ]
            ],
            [
                'title' => '商场订单下单量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inToday('created_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inWeek('created_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inMonth('created_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inYesterday('created_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inLastWeek('created_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inLastMonth('created_at')->count(),
                    ],
                ]
            ],
            [
                'title' => '商场订单支付量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inToday('payed_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inMonth('payed_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inYesterday('payed_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inLastWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::inLastMonth('payed_at')->count(),
                    ],
                ]
            ],
            [
                'title' => '商场订单完成量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inToday('confirmed_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inWeek('confirmed_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inMonth('confirmed_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inYesterday('confirmed_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inLastWeek('confirmed_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/orders',
                        'info' => Order::where('status', 4)->inLastMonth('confirmed_at')->count(),
                    ],
                ]
            ],
        ];

        foreach ($list as $item) {
            $content_c = new Content();
            $content_c->row(function (Row $row) use ($item) {
                foreach ($item['data'] as $box) {
                    $row->column($item['width'], function (Column $column) use ($box) {
                        $column->append(new InfoBox($box['title'], $box['icon'], $box['color'], $box['link'], $box['info']));
                    });
                }
            });
            $content->row(new Box($item['title'], $content_c->build()));
        }

        return $content;
    }
}
