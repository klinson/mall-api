<?php
namespace App\Admin\Extensions\Exporters;

use App\Models\Order;
use App\Models\RefundOrder;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class OrderExporter extends AbstractExporter
{
    protected $fileName = '订单列表';
    protected $title1 = [];
    protected $title2 = [
    ];

    public function __construct(Grid $grid = null)
    {
        $title1 = [
            '订单ID', '订单号', '订单用户', __('All price'), __('Goods price'), __('Real price'), __('Coupon price'), __('Freight price'), '订单状态', '下单时间', '支付时间', '备注','配送地址','订单商品信息'
        ];
        $title2 = [
            '商品ID', '商品名称', '商品规格', '营销', '价格', '销售量', '总售价', '退款状态'
        ];
        $this->title1 = $title1;
        array_pop($title1);
        $this->title2 = array_merge($title1, $title2);

        parent::__construct($grid);
    }

    public function export()
    {
        set_time_limit(0);
        Excel::create($this->fileName . '-' . date('YmdHis'), function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $row_number = 1;
                // 头部设置
                $sheet->appendRow($row_number++, $this->title1);
                $sheet->appendRow($row_number++, $this->title2);

                $A = ord('A');
                $title1_count = count($this->title1);
                $title2_count = count($this->title2);

                $merge_col_arr = range(0, $title1_count-2);
                foreach ($merge_col_arr as &$item) {
                    $item = chr($A+$item);
                }
                unset($item);
                $sheet->setMergeColumn([
                    'columns' => $merge_col_arr,
                    'rows' => [
                        [1, 2]
                    ]
                ]);

                $sheet->mergeCells(chr($A+$title1_count-1).'1:'.chr($A+$title2_count-1).'1');

                // 数据设置
                // 这段逻辑是从表格数据中取出需要导出的字段
                $this->chunk(function ($orders) use ($sheet, &$row_number, $merge_col_arr) {
                    $orders->map(function ($order) use ($sheet, &$row_number, $merge_col_arr) {
                        $row_order = [
                            $order->id,
                            strval($order->order_number),
                            $order->user->nickname,
                            strval($order->all_price * 0.01),
                            strval($order->goods_price * 0.01),
                            strval($order->real_price * 0.01),
                            strval($order->coupon_price * 0.01),
                            strval($order->freight_price * 0.01),
                            Order::status_text[$order->status],
                            $order->created_at->toDateTimeString(),
                            $order->payed_at,
                            $order->remarks,
                            $order->address_snapshot['name'].'|'.$order->address_snapshot['mobile'].'|'.$order->address_snapshot['city_name'].'-'.$order->address_snapshot['address'],
                        ];

                        $merge_row_start = $row_number;
                        foreach ($order->orderGoods as $orderGood) {
                            $row = [
                                $orderGood->goods_id,
                                $orderGood->snapshot['goods']['title'],
                                '无',
                                strval($orderGood->price * 0.01),
                                $orderGood->quantity,
                                strval($orderGood->quantity * $orderGood->price * 0.01),
                                RefundOrder::status_text[$orderGood->refund_status],
                            ];

                            $row = array_merge($row_order, $row);
                            $sheet->appendRow($row_number++, $row);
                        }

                        $merge_row_end = $row_number - 1;

                        $sheet->setMergeColumn([
                            'columns' => $merge_col_arr,
                            'rows' => [
                                [$merge_row_start, $merge_row_end]
                            ]
                        ]);
                    });
                }, 1000);
            });

        })->export('xlsx');
    }
}