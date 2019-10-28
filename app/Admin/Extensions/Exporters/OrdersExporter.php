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
        'title'   => '标题',
        'content' => '内容',
    ];
}