<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-6-15
 * Time: 上午9:39
 */

namespace App\Models\Traits;


use Carbon\Carbon;

trait ScopeDateHelper
{
    // 今日筛选
    public function scopeInToday($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()]);
    }

    // 昨日筛选
    public function scopeInYesterday($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->subDay()->startOfDay()->toDateTimeString(), Carbon::now()->subDay()->endOfDay()->toDateTimeString()]);
    }

    // 前N天筛选
    public function scopeInLastDays($query, $fieldName, $days = 1)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->subDay($days)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()]);
    }

    // 本周筛选
    public function scopeInWeek($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()]);
    }

    // 上一周
    public function scopeInLastWeek($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->subWeek()->startOfWeek()->toDateTimeString(), Carbon::now()->subWeek()->endOfWeek()->toDateTimeString()]);
    }

    // 前N周筛选
    public function scopeInLastWeeks($query, $fieldName, $weeks = 1)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->subWeek($weeks)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()]);
    }

    // 本月
    public function scopeInMonth($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()]);
    }

    // 上个月
    public function scopeInLastMonth($query, $fieldName)
    {
        return $query->whereBetween($fieldName, [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->subMonth()->endOfMonth()->toDateTimeString()]);
    }

}