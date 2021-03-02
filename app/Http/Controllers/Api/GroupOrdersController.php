<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2021/3/2
 * Time: 00:20
 */

namespace App\Http\Controllers\Api;

use App\Models\GroupOrder;
use App\Transformers\GroupOrderTransformer;
use Illuminate\Http\Request;

class GroupOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = GroupOrder::query();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }

        $query->isMine();
        $list = $query->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new GroupOrderTransformer());
    }

    public function show(GroupOrder $order)
    {
        $this->authorize('is-mine', $order);

        return $this->response->item($order, new GroupOrderTransformer());
    }

}