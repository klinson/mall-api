<?php

namespace App\Models;

use App\Jobs\AddLotteryChanceJob;
use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;
use DB;

class LotteryChance extends Model
{
    use SoftDeletes, HasOwnerHelper;

    const redis_cache_key = 'lottery_chance_count';

    protected $fillable = [
        'user_id', 'type', 'description', 'used_at', 'by_user_id'
    ];

    // 获取抽奖机会方式
    const FIRST_LOGIN_TYPE = 1;
    const INVITE_USER_REGISTER_TYPE = 2;
    const SYSTEM_PRESENT = 3;
    const INVITE_USER_FAVOUR3GOODS = 4;
    const SELF_FAVOUR3GOODS = 5;

    // 获取方式对应可获取抽奖机会次数, -1不限制
    const TYPE_LIMIT_COUNTS = [
        self::FIRST_LOGIN_TYPE => 0,
        self::INVITE_USER_REGISTER_TYPE => 0,
        self::INVITE_USER_FAVOUR3GOODS => 3,
        self::SELF_FAVOUR3GOODS => 1,
        self::SYSTEM_PRESENT => -1,
    ];

    // 获取抽奖机会方式对应中文注释
    const DESCRIPTIONS = [
        self::FIRST_LOGIN_TYPE => '用户注册',
        self::INVITE_USER_REGISTER_TYPE => '邀请用户成功注册',
        self::SYSTEM_PRESENT => '系统赠送',
        self::INVITE_USER_FAVOUR3GOODS => '邀请用户注册并收藏3个商品',
        self::SELF_FAVOUR3GOODS => '收藏3个商品',
    ];


    public static function getChance($user_id)
    {
        return self::where('user_id', $user_id)->orderBy('id')->unused()->first();
    }

    public static function generateChance($user_id, $type, $by_user_id = 0)
    {
        DB::beginTransaction();
        $chance = new self([
            'user_id' => $user_id,
            'by_user_id' => $by_user_id,
            'type' => $type,
        ]);
        $chance->save();

        Redis::hincrby(self::redis_cache_key, $user_id, 1);
        DB::commit();
        return $chance;
    }

    // 系统赠送
    public static function present($user, $count = 1)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        $number = $count;
        $res = [];
        while ($number) {
            $res[] = self::generateChance($user_id, self::SYSTEM_PRESENT);
            $number--;
        }
        return $res;
    }

    public static function whenRegister($user)
    {
        dispatch(new AddLotteryChanceJob($user->id, self::FIRST_LOGIN_TYPE));

        if ($user->inviter_id) {
            dispatch(new AddLotteryChanceJob($user->id, self::INVITE_USER_REGISTER_TYPE));
        }
    }

    public static function whenFavourGoods($user)
    {
        dispatch(new AddLotteryChanceJob($user->id, self::SELF_FAVOUR3GOODS));

        if ($user->inviter_id) {
            dispatch(new AddLotteryChanceJob($user->id, self::INVITE_USER_FAVOUR3GOODS));
        }
    }

    // 用户注册赠送一次
    public static function whenEvent($user, $event)
    {
        if (! ($user instanceof User)) {
            $user = User::find(intval($user));
        }
        if (! $user) {
            return false;
        }
        $by_user_id = $user->id;
        $user_id = $user->id;
        switch ($event) {
            case self::SYSTEM_PRESENT:
                break;
            case self::FIRST_LOGIN_TYPE:
                if (self::overCount($user_id, $event)) {
                    return false;
                }
                break;
            case self::INVITE_USER_REGISTER_TYPE:
                if (! $user->inviter) {
                    return false;
                }
                $user_id = $user->inviter->id;
                if (self::overCount($user_id, $event, $by_user_id)) {
                    return false;
                }
                break;
            case self::SELF_FAVOUR3GOODS:
                if ($user->favourGoods()->count() < 3) {
                    return false;
                }
                if (self::overCount($user_id, $event)) {
                    return false;
                }
                break;
            case self::INVITE_USER_FAVOUR3GOODS:

                if ($user->favourGoods()->count() < 3) {
                    return false;
                }
                if (! $user->inviter) {
                    return false;
                }
                $user_id = $user->inviter->id;

                if (self::overCount($user_id, $event, $by_user_id)) {
                    return false;
                }
                break;
        }

        return self::generateChance($user_id, $event, $by_user_id);
    }

    // 验证指定方式的获得机会次数是否超了
    public static function overCount($user_id, $type, $by_user = 0)
    {
        if (self::getLimitCount($type) === -1) {
            return false;
        }
        if (self::getCount($user_id, $type) >= self::getLimitCount($type)) {
            return true;
        } else {
            if ($by_user && self::getCount($user_id, $type, $by_user)) {
                return true;
            }
            return false;
        }
    }

    // 获取指定方式的获得机会次数
    public static function getCount($user_id, $type, $by_user = 0)
    {
        $query = self::where('user_id', $user_id)->where('type', $type);
        if ($by_user) {
            $query->where('by_user_id', $by_user);
        }
        return $query->count();
    }

    // 获取指定方式的获得机会的限制次数
    public static function getLimitCount($type)
    {
        return isset(self::TYPE_LIMIT_COUNTS[$type]) ? self::TYPE_LIMIT_COUNTS[$type] : 0;
    }

    public static function getUnusedCount($user_id)
    {
        return self::where('user_id', $user_id)->unused()->count();
    }

    public static function getUnusedCountByCache($user_id)
    {
        return intval(Redis::hget(self::redis_cache_key, $user_id));
    }

    // 重置缓存中的抽奖次数，慎重操作
    public static function resetRedisCacheCount()
    {
        DB::table('lottery_chances')->whereNull('used_at')->groupBy('user_id')->orderBy('user_id')->select(['user_id', DB::raw('count(*) as count')])->having('count', '>', 0)->chunk(100, function ($users) {
            Redis::pipeline(function ($pipe) use ($users) {
                foreach ($users  as $user) {
                    $pipe->hset(self::redis_cache_key, $user->user_id, $user->count);
                }
            });
        });
    }

    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }

    // 设置已使用
    public function setUsed()
    {
        if ($this->used_at) {
            return false;
        } else {
            $this->used_at = date('Y-m-d H:i:s');
            $this->save();
            return $this;
        }
    }

    /**
     * @param $user_id
     * @return bool
     * @author klinson <klinson@163.com>
     */
    public static function useOne($user_id)
    {
        $number = intval(Redis::hincrby(self::redis_cache_key, $user_id, -1));
        if ($number < 0) {
            Redis::hincrby(self::redis_cache_key, $user_id, 1);
            return false;
        } else {
            return true;
        }
    }

    public function byUser()
    {
        return $this->belongsTo(User::class, 'by_user_id');
    }
}
