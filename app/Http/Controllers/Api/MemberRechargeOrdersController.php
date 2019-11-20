<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\MemberRechargeActivity;
use App\Models\MemberRechargeOrder;
use App\Models\User;
use App\Transformers\MemberLevelTransformer;
use App\Transformers\MemberRechargeActivityTransformer;
use App\Transformers\MemberRechargeOrderTransformer;
use Illuminate\Http\Request;

class MemberRechargeOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = MemberRechargeOrder::query();

        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }
        $page = $query->isOwner()->hasPayed()->recent()->paginate($request->per_page);
        return $this->response->paginator($page, new MemberRechargeOrderTransformer());
    }

    public function show(MemberRechargeOrder $order)
    {
        $this->authorize('is-mine', $order);

        return $this->response->item($order, new MemberRechargeOrderTransformer());
    }

    public function store(Request $request)
    {
        $activity = MemberRechargeActivity::find($request->activity_id);

        if (empty($activity)) {
            return $this->response->errorBadRequest('充值活动不存在');
        }

        // 判断当前是否已经拥有比之更高级的会员
        if ($max_level = \Auth::user()->validMemberLevels()->max('level')) {
            if ($activity->level < $max_level) {
                return $this->response->errorBadRequest('当前会员等级已高于或等于此充值活动的会员等级');
            }
        }

        // 判断当前会员是否已经永久了
        $last_member_level = \Auth::user()->getLastMemberLevel($activity->member_level_id);
        if ($last_member_level && is_null($last_member_level->validity_ended_at)) {
            return $this->response->errorBadRequest('您已经拥有永久期限的当前会员');
        }

        $inviter_id = 0;
        if ($request->inviter_id && ($inviter = User::checkInviter($request->inviter_id))) {
            $inviter_id = $inviter->id;
        }

        $order = MemberRechargeOrder::generateOrder(\Auth::user(), $activity, $inviter_id);

        try {
            if (app()->isLocal()) {
                $order->setSuccess();
                return $this->response->item($order, new MemberRechargeOrderTransformer());
            } else {
                $config = $order->generatePayConfig();
                return $this->response->array($config);
            }
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }

    }
}