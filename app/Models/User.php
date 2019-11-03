<?php

namespace App\Models;

use App\Models\Traits\IntTimestampsHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const SEX2TEXT = [
        '未知', '男', '女'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname', 'sex', 'wxapp_openid', 'mobile', 'has_enabled', 'avatar', 'wxapp_userinfo', 'inviter_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    // 注册用户初始化
    protected static function boot()
    {
        self::created(function ($model) {
            $model->init();
        });

        parent::boot();
    }

    /**
     * sub 内容
     * @author klinson <klinson@163.com>
     * @return mixed 默认返回当前主键的值
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * PAYLOAD 附加其他内容配置
     * @author klinson <klinson@163.com>
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function coffer()
    {
        return $this->hasOne(Coffer::class, 'user_id', 'id');
    }

    public function init()
    {
        $this->wallet()->create();
    }

    public function agency()
    {
        return $this->belongsTo(AgencyConfig::class, 'agency_id', 'id');
    }

    public function isAgency()
    {
        return ! ($this->agency_id === 0);
    }

    // 代理初始化
    public function agencyInit()
    {
        if (! $this->coffer) {
            $this->coffer()->create();
        }
    }

    // 会员初始化
    public function memberInit()
    {
        if (! $this->coffer) {
            $this->coffer()->create();
        }
    }

    // 是否是会员
    public function isMember()
    {
        return ($this->validMemberLevels()->count() > 0);
    }

    public function memberLevels()
    {
        return $this->hasMany(UserHasMemberLevel::class, 'user_id', 'id')->orderBy('level', 'desc');
    }

    public function validMemberLevels()
    {
        return $this->memberLevels()->where(function ($query) {
            $query->where('validity_ended_at', '>', Carbon::now()->toDateTimeString())
                ->orWhereNull('validity_ended_at');
        });
    }

    // 获取用户当前会员最大折扣
    public function getMaxMemberDiscount()
    {
        // 100->原价 10折
        $discount = 100;
        if ($this->validMemberLevels) {
            foreach ($this->validMemberLevels as $memberLevel) {
                // 88->8.8折
                if ($memberLevel->member_level_snapshot['discount'] < $discount) {
                    $discount = $memberLevel->member_level_snapshot['discount'];
                }
            }
        }
        return $discount;
    }
}
