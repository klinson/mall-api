<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use App\Transformers\MemberRechargeOrderTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserHasMemberLevel extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $casts = [
        'member_level_snapshot' => 'array',
        'order_snapshot' => 'array'
    ];

    public static function generate(MemberRechargeOrder $order)
    {
        $model = new self();
        $model->user_id = $order->user_id;
        $model->level = $order->member_level_snapshot['level'];
        $model->member_level_id = $order->member_level_id;
        $model->member_level_snapshot = $order->member_level_snapshot;
        $model->member_recharge_order_id = $order->id;
        $model->order_snapshot = (new MemberRechargeOrderTransformer())->transform($order);
        $model->validity_started_at = $order->validity_started_at;
        $model->validity_ended_at = $order->validity_ended_at;
        $model->save();
        return $model;
    }

    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class);
    }

    public function order()
    {
        return $this->belongsTo(MemberRechargeOrder::class, 'member_recharge_order_id');
    }
}
