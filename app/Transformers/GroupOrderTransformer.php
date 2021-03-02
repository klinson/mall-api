<?php

namespace App\Transformers;

use App\Models\GroupOrder;
use League\Fractal\TransformerAbstract;

class GroupOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['owner', 'admin'];

    public function transform(GroupOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'admin_id' => $order->admin_id,
            'order_number' => $order->order_number,
            'all_price' => $order->all_price,
            'remarks' => $order->remarks,
            'status' => $order->status,
            'status_text' => GroupOrder::status_text[$order->status] ?? '',
            'created_at' => $order->created_at->toDatetimeString(),
            'payed_at' => $order->payed_at,
        ];
    }

    public function includeOwner(GroupOrder $order)
    {
        return $this->item($order->owner, new UserTransformer());
    }

    public function includeAdmin(GroupOrder $order)
    {
        return $this->item($order->admin, new AdminUserTransformer());
    }

}