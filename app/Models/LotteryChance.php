<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryChance extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'user_id', 'type', 'description', 'used_at'
    ];

    // 获取抽奖机会方式
    const FIRST_LOGIN_TYPE = 1;
    const INVITE_USER_REGISTER_TYPE = 2;
    const SYSTEM_PRESENT = 3;

    // 获取方式对应可获取抽奖机会次数, -1不限制
    const TYPE_LIMIT_COUNTS = [
        self::FIRST_LOGIN_TYPE => 1,
        self::INVITE_USER_REGISTER_TYPE => 2,
        self::SYSTEM_PRESENT => -1,
    ];

    // 获取抽奖机会方式对应中文注释
    const DESCRIPTIONS = [
        self::FIRST_LOGIN_TYPE => '用户首次注册赠送',
        self::INVITE_USER_REGISTER_TYPE => '邀请用户注册',
        self::SYSTEM_PRESENT => '系统赠送',
    ];


    public static function getMyChance()
    {
        return self::isOwner()->unused()->first();
    }

    public static function generateChance($user_id, $type)
    {
        $chance = new self([
            'user_id' => $user_id,
            'type' => $type,
        ]);
        $chance->save();

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

    public static function whenInviteUserRegister($user)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }
        if (self::overCount($user_id, self::INVITE_USER_REGISTER_TYPE)) {
            return false;
        }

        return self::generateChance($user, self::INVITE_USER_REGISTER_TYPE);
    }

    // 用户注册赠送一次
    public static function whenUserFirstLogin($user)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }
        if (self::overCount($user_id, self::FIRST_LOGIN_TYPE)) {
            return false;
        }

        return self::generateChance($user, self::FIRST_LOGIN_TYPE);
    }

    // 验证指定方式的获得机会次数是否超了
    public static function overCount($user_id, $type)
    {
        if (self::getLimitCount($type) === -1) {
            return false;
        }
        if (self::getCount($user_id, $type) >= self::getLimitCount($type)) {
            return true;
        } else {
            return false;
        }
    }

    // 获取指定方式的获得机会次数
    public static function getCount($user_id, $type)
    {
        return self::where('user_id', $user_id)->where('type', $type)->count();
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
}
