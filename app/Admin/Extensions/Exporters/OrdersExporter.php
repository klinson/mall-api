<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/10/26
 * Time: 20:05
 */

namespace App\Admin\Extensions\Exporters;

use Encore\Admin\Grid\Exporters\ExcelExporter;

class OrdersExporter extends ExcelExporter
{
    protected $fileName = '订单导出YmdHis.xlsx';

    protected $columns = [
        'id'      => 'ID',
        'order_number'   => '订单号',
        'all_price' => __('All price'),
        'goods_price' => __('Goods price'),
        'real_price' => __('Real price'),
        'coupon_price' => __('Coupon price'),
        'freight_price' => __('Freight price')
        'status' => '订单状态',
        'remarks' => '备注',
        'address_snapshot' => '配送信息',
        'created_at' => '下单时间',
        'payed_at' => '付款时间',
    ];
}