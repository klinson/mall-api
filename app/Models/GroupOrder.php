<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;
    const hasDefaultObserver = true;

    const status_text = [
        '未下单', '待支付', '已完成', '已取消'
    ];

    public function whenCreating()
    {
        $this->admin_id = Admin::user()->id;
        $this->status = 1;
    }

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    /**
     * 设置已支付
     * @return bool
     * @author klinson <klinson@163.com>
     */
    public function pay()
    {
        if ($this->status != 1) return false;
        $this->status = 2;
        $this->payed_at = date('Y-m-d H:i:s');
        $this->save();
        return true;
    }

    /**
     * 设置已取消订单
     * @return bool
     * @author klinson <klinson@163.com>
     */
    public function cancel()
    {
        if ($this->status != 1) return false;
        $this->status = 3;
        $this->save();
        return true;
    }
}
