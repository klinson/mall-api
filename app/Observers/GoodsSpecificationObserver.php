<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/17
 * Time: 23:52
 */

namespace App\Observers;


use App\Models\GoodsSpecification;

class GoodsSpecificationObserver
{
    public function saved(GoodsSpecification $model)
    {
        if (! $model->thumbnail) {
            $model->thumbnail = $model->goods->thumbnail;
            $model->save();
        }
        $model->goods->autoUpdate();
    }

    public function deleted(GoodsSpecification $model)
    {
        $model->goods->autoUpdate();
    }
}