<?php

namespace App\Transformers;

use App\Models\OfflineOrder;
use League\Fractal\TransformerAbstract;

class OfflineOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['staff', 'owner', 'store'];

    public function transform(OfflineOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'staff_id' => $order->staff_id,
            'order_number' => $order->order_number,
            'all_price' => $order->all_price,
            'real_price' => $order->real_price,
            'remarks' => $order->remarks,
            'status' => $order->status,
            'status_text' => OfflineOrder::status_text[$order->status] ?? '',
            'created_at' => $order->created_at->toDatetimeString(),
            'confirmed_at' => $order->confirmed_at,
            'payed_at' => $order->payed_at,
            'used_integral' => $order->used_integral,
            'used_balance' => $order->used_balance,
            'real_cost' => $order->real_cost,
        ];
    }

    public function includeOwner(OfflineOrder $order)
    {
        if (! $order->owner) return null;
        return $this->item($order->owner, new UserTransformer());
    }

    public function includeStaff(OfflineOrder $order)
    {
        return $this->item($order->staff, new UserTransformer());
    }

    public function includeStore(OfflineOrder $order)
    {
        return $this->item($order->store, new StoreTransformer());
    }
}