<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-9-12
 * Time: 下午8:55
 */

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use App\Models\GoodsSpecification;
use App\Models\Press;
use App\Models\User;
use App\Models\UserHasCoupon;
use App\Transformers\CouponTransformer;
use App\Transformers\UserHasCouponTransformer;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function index()
    {
        $m = new Press();
        $m->import();
        dd();
        $list = Coupon::enabled()->withCount('myCoupons')->get();
        return $this->response->collection($list, new CouponTransformer('my_coupons_count'));
    }

    public function show(Coupon $coupon)
    {
        return $this->response->item($coupon, new CouponTransformer());
    }

    /**
     * 我的优惠券（下单前计算折扣优惠）
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @author klinson <klinson@163.com>
     */
    public function myCoupons(Request $request)
    {
        //status 状态1可用，2过期，3已用，4冻结,0未生效
        $query = UserHasCoupon::isOwner()->recent();
        if ($request->price) {
            $query->whereIn('status', [0, 1]);
        } else {
            switch ($request->status) {
                case 1:
                    $query->whereIn('status', [0, 1]);
                    break;
                case 2:
                    $query->whereIn('status', [0, 1, 2]);
                    break;
                case -1:
                    break;
                default:
                    if (in_array($request->status, [3, 4])) {
                        $query->where('status', $request->status);
                    } else {
                        $request->status = null;
                    }
                    break;
            }
        }

        $userCoupons = $query->get();
        $list = [];
        foreach ($userCoupons as $key=>$coupon) {
            $coupon = $coupon->updateStatus();
            if ($request->price) {
                if (! in_array($coupon->status, [0, 1])) continue;

                // 计算折扣
                $coupon->discount_money = $coupon->settleDiscount($request->price);
            } else {
                if ($request->status) {
                    if ($request->status == -1) {
                        if ($coupon->status == 1) continue;
                    } else {
                        if ($coupon->status != $request->status) continue;
                    }
                }
            }

            $list[] = $coupon;
        }

        $list = collect($list);
        if ($request->price) {
            $list = $list->sortByDesc('discount_money');
        }

        return $this->response->collection($list, new UserHasCouponTransformer());
    }

    public function checkUserCoupons(Request $request)
    {
        $userCoupons = UserHasCoupon::isOwner()->where('status', 1)->recent()->get();
        if (empty($request->goods_list)) {
            return $this->response->errorBadRequest('请选择商品');
        }

        $goods_ids_list = $request->goods_list;
        if (blank($goods_ids_list) || ! is_array($goods_ids_list)) {
            return $this->response->errorBadRequest('下单商品不能为空');
        }
        $goods_specification_id2info = [];
        foreach ($goods_ids_list as $goods) {
            if (! isset($goods['goods_id']) || ! is_numeric($goods['goods_id']) || $goods['goods_id'] <= 0) {
                return $this->response->errorBadRequest('存在商品id不合法');
            }
            if (! isset($goods['goods_specification_id']) || ! is_numeric($goods['goods_specification_id']) || $goods['goods_specification_id'] <= 0) {
                return $this->response->errorBadRequest('存在商品规格id不合法');
            }
            if (! isset($goods['quantity']) || ! is_numeric($goods['quantity']) || $goods['quantity'] <= 0) {
                return $this->response->errorBadRequest('存在商品购买数量不合法');
            }
            $goods_specification_id2info[$goods['goods_specification_id']] = $goods;
        }

        // 排它锁 锁表 lockForUpdate
        $goods_specification_list = GoodsSpecification::with(['goods'])
            ->enabled()
            ->whereIn('id', array_keys($goods_specification_id2info))
            ->get();
        $goods_specification_by_key_list = $goods_specification_list->keyBy('id');

        // 获取用户会员折扣
        $member_discount = \Auth::user()->getBestMemberDiscount(true);

        $all_goods_price = 0;
        $all_member_discount_price = 0;

        foreach ($goods_ids_list as $info) {
            $goods_id = intval($info['goods_id']);
            $specification_id = $info['goods_specification_id'];
            $specification = $goods_specification_by_key_list[$specification_id];
            if ($specification->goods_id != $goods_id) {
                // 回滚数据
                return $this->response->errorBadRequest('存在商品规格不合法');
            }

            $goods_price = $specification->price;

            // 计算单品会员优惠
            $item_goods_all_price = $goods_price * $info['quantity'];
            if ($member_discount == 100) {
                $item_goods_real_price = $item_goods_all_price;
            } else {
                $item_goods_real_price = ceil(strval($item_goods_all_price * $member_discount * 0.01));
            }

            $all_member_discount_price += $item_goods_real_price;
            $all_goods_price += $item_goods_all_price;
        }

        $all_goods_price = intval(strval($all_goods_price));
        $all_member_discount_price = intval(strval($all_member_discount_price));

        // 对会员优惠后的价格进行满减
        $price = $all_member_discount_price;

        $list = collect();
        foreach ($userCoupons as $coupon) {
            $coupon->discount_money = $coupon->settleDiscount($price);
            if ($coupon->discount_money > 0) {
                $list->push($coupon);
            }
        }

        $list = $list->sortByDesc('discount_money');
        return $this->response->collection($list, new UserHasCouponTransformer());
    }

    // 领取优惠券
    public function draw(Coupon $coupon)
    {
        $this->authorize('enabled', $coupon);

        try {
            // 验证是否可以领取
            $coupon->checkDraw();

            $userCouponCount = $coupon->myCoupons()->count();
            if ($userCouponCount >= $coupon['limit']) return $this->response->errorBadRequest('您已经超过领取限制了');

            $list = $coupon->toUser($this->user, '用户领取', 1);
            return $this->response->item($list[0], new UserHasCouponTransformer());
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    // 赠送（生产环境不可用，便于测试)
    public function present(Coupon $coupon, Request $request)
    {
        if (! \App::environment(['local', 'dev', 'development'])) {
            return $this->response->errorBadRequest('当前环境不支持');
        }

        $this->authorize('enabled', $coupon);

        if (empty($request->user_id) || ! $user = User::find($request->user_id)) {
            return $this->response->errorBadRequest('未选择赠送指定人');
        }

        $count = $request->count ?: 1;

        try {
            // 验证是否可以领取
            $coupon->checkDraw();

            $coupon->present($user, $count);
            return $this->response->noContent();
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }
}