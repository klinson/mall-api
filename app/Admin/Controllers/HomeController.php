<?php

namespace App\Admin\Controllers;

use App\Models\CofferWithdrawal;
use App\Models\GroupOrder;
use App\Models\LotteryRecord;
use App\Models\OfflineOrder;
use App\Models\Order;
use App\Models\RefundOrder;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field\Divider;
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
                        'title' => '普通订单待发货',
                        'icon' => 'cart-plus',
                        'color' => 'red',
                        'link' => '/admin/orders?status[]=2',
                        'info' => Order::where('status', 2)->count(),
                    ],
                    [
                        'title' => '普通订单待自提',
                        'icon' => 'cart-plus',
                        'color' => 'green',
                        'link' => '/admin/orders?status[]=8',
                        'info' => Order::where('status', 8)->count(),
                    ],
                    [
                        'title' => '团购订单待支付',
                        'icon' => 'cart-plus',
                        'color' => 'yellow',
                        'link' => '/admin/groupOrders?status[]=1',
                        'info' => GroupOrder::where('status', 1)->count(),
                    ],
                ]
            ],
            [
                'big_title' => '普通订单统计',
                'title' => '下单量',
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
                'title' => '支付量',
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
                'title' => '完成量',
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

            [
                'big_title' => '线下订单统计',
                'title' => '成交量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inToday('payed_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inMonth('payed_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inYesterday('payed_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inLastWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/offlineOrders',
                        'info' => OfflineOrder::inLastMonth('payed_at')->count(),
                    ],
                ],
            ],

            [
                'big_title' => '团购订单统计',
                'title' => '下单量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inToday('created_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inWeek('created_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inMonth('created_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inYesterday('created_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inLastWeek('created_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inLastMonth('created_at')->count(),
                    ],
                ],
            ],
            [
                'title' => '成交量',
                'width' => 4,
                'data' => [
                    [
                        'title' => '今日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inToday('payed_at')->count(),
                    ],
                    [
                        'title' => '本周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '本月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inMonth('payed_at')->count(),
                    ],
                    [
                        'title' => '昨日',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inYesterday('payed_at')->count(),
                    ],
                    [
                        'title' => '上周',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inLastWeek('payed_at')->count(),
                    ],
                    [
                        'title' => '上月',
                        'icon' => 'cart-plus',
                        'color' => 'aqua',
                        'link' => '/admin/groupOrders',
                        'info' => GroupOrder::inLastMonth('payed_at')->count(),
                    ],
                ],
            ],
        ];

        foreach ($list as $item) {
            // 大标题
            if ($item['big_title'] ?? '') {
                $content->row(new Divider("————————  {$item['big_title']}  ————————"));
            }

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
