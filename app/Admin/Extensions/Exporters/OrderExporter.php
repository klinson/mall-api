<?php
namespace App\Admin\Extensions\Exporters;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class OrderExporter extends AbstractExporter
{
    protected $fileName = '订单列表';
    protected $title1 = [
        'ID', '订单号', '订单用户', __('All price'), __('Goods price'), __('Real price'), __('Coupon price'), __('Freight price'), '订单状态', '下单时间', '支付时间', '订单商品信息'
    ];
    protected $title2 = [
        '商品名称', '商品规格', '营销', '价格', '销售量', '总售价', '是否退款'
    ];

    public function header($excel, &$row_number)
    {
        // 头部设置
        $sheet->appendRow($row_number++, $this->title1);
        $sheet->appendRow($row_number++, array_values($this->title1 + $this->title2));

        $A = ord('A');
        $title1_count = count($this->title1);
        $title2_count = count($this->title2);

        $merge_col_arr = range(0, $title1_count);
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

        $sheet->mergeCells(chr($A+$title1_count+1).'1:'.chr($A+$title1_count+$title2_count).'1');

    }

    public function export()
    {
        set_time_limit(0);
        Excel::create($this->fileName . '-' . date('YmdHis'), function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $row_number = 1;
                $A = ord('A');

                $this->header($sheel, $row_number);

                // 数据设置
                // 这段逻辑是从表格数据中取出需要导出的字段
                $this->chunk(function ($orders) use ($sheet, &$row_number, $merge_col_arr) {
                    $orders->map(function ($order) use ($sheet, &$row_number, $merge_col_arr) {
                        $item = $order->toArray();
                        $refund_price = 0;
                        $goods_list = array_map(function ($goods) use (&$refund_price) {
                            if ($goods['is_refund'] === 2) {
                                $refund_price += $goods['quantity'] * $goods['price'];
                            }

                            $price = $goods['price']*0.01;
                            return [
                                $goods['goods_info']['title'],
                                $goods['goods_info']['goods_specification_title'],
                                $goods['marketing_type'] ? MARKETING_TYPE_TITLE[$goods['marketing_type']] : '零售',
                                $price,
                                $goods['quantity'],
                                $price * $goods['quantity'],
                                $goods['is_refund'] === 2 ? "已退款" : "无"
                            ];
                        }, $item['order_goods']);

                        $merge_row_start = $row_number;
                        $row_order = [
                            $item['id'],
                            $item['order_number'],
                            $item['area']['long_title'],
                            $item['community']['title'],
                            "{$item['user']['nickname']}（{$item['user']['mobile']}）",
                            strval($item['price'] * 0.01),
                            strval($refund_price * 0.01),
                            strval(($item['price'] - $refund_price) * 0.01),
                            ORDER_TYPE[$item['order_type']],
                            ORDER_STATUS[$item['status']],
                            $item['created_at'],
                            $item['payed_at'],
                            $item['confirmed_at'],
                        ];
                        foreach ($goods_list as $goods) {
                            $row = array_merge($row_order, $goods);
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