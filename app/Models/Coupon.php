<?php

namespace App\Models;

use App\Transformers\CouponTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    use SoftDeletes;
    const type_text = [
        1 => '折扣券',
        2 => '满减券',
    ];
    protected $appends = ['face_value_text'];

    public function getFaceValueTextAttribute()
    {
        // 2满减固定金额券，1打固定折扣券
        switch ($this->type) {
            case 1:
                return '打' . strval($this->face_value * 0.1) . '折';
            case 2:
                return '减￥' . strval($this->face_value * 0.01);
            default:
                return '';
        }
    }

    /**
     * 验证是否可以发领取
     * @return bool
     * @throws \Exception
     * @author klinson <klinson@163.com>
     */
    public function checkDraw()
    {
        // 领取前校验
        if ($this['quantity'] <= 0) throw new \Exception('优惠券已抢光，下次再来吧~');
        $now = date('Y-m-d H:i:s');
        if (!is_null($this['draw_started_at']) && $this['draw_started_at'] > $now) throw new \Exception('优惠券还未开始领取');
        if (!is_null($this['draw_ended_at']) && $this['draw_ended_at'] < $now) throw new \Exception('优惠券领取已结束');
        return true;
    }

    public function toUser($user, $description, $count = 1)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        try {
            $now = date('Y-m-d H:i:s');
            switch ($this['status']) {
                case 0:
                    if (is_null($this['valid_started_at'])) {
                        $status = 1;
                    } else if ($now >= $this['valid_started_at']) {
                        $status = 1;
                    } else {
                        $status = 0;
                        break;
                    }
                case 1:
                    if (! is_null($this['valid_ended_at']) && $now >= $this['valid_ended_at']) {
                        $status = 2;
                    }
            }

            $item = [
                'coupon_snapshot' => $this->toArray(),
                'user_id' => $user_id,
                'status' => $status,
                'discount_money' => 0,
                'has_enabled' => 1,
                'description' => $description
            ];
            $list = [];
            while ($count--) {
                $list[] = $item;
            }

            DB::beginTransaction();
            $this->decrement('quantity', $count);
            $this->save();
            if ($this->quantity <= 0) {
                DB::rollBack();
                throw new \Exception('优惠券已抢光~');
            }
            $res = $this->userCoupons()->createMany($list);
            DB::commit();
            return $res;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
    }

    // 系统赠送
    public function present($user, $count = 1)
    {
        $this->toUser($user, '系统赠送(API)', $count);
    }

    public function userCoupons()
    {
        return $this->hasMany(UserHasCoupon::class, 'coupon_id');
    }

    public function myCoupons()
    {
        return $this->userCoupons()->isMine();
    }
}
