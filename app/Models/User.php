<?php

namespace App\Models;

use App\Models\Traits\IntTimestampsHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use phpDocumentor\Reflection\Types\Self_;
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
        })->orderBy('validity_started_at');
    }

    public function getRealMemberLevelsAttribute()
    {
        $res = collect();
        if ($this->validMemberLevels->isNotEmpty()) {
            $memberLevels = $this->validMemberLevels->groupBy('member_level_id');
            foreach ($memberLevels as $memberLevelArr) {
                if (count($memberLevelArr) === 1) {
                    $res->push($memberLevelArr[0]);
                } else {
                    $last = $memberLevelArr->last();
                    $first = $memberLevelArr[0];
                    $first->validity_ended_at = $last->validity_ended_at;
                    $res->push($first);
                }
            }
        }
        return $res;
    }

    // 获取用户当前会员最佳折扣
    public function getBestMemberDiscount($reset = false)
    {
        $cache_key = 'user_member_best_discount_'.$this->id;

        if ($reset || app()->isLocal()) cache()->delete($cache_key);

        return cache()->remember($cache_key, 10, function () {
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
            return intval($discount);
        });
    }

    public function hasFeeFreight()
    {
        $is_fee_freight = 0;
        if ($this->validMemberLevels) {
            foreach ($this->validMemberLevels as $memberLevel) {
                // 88->8.8折
                if ($memberLevel->member_level_snapshot['is_fee_freight']) {
                    $is_fee_freight = 1;
                    break;
                }
            }
        }

        return $is_fee_freight;
    }

    public function inviter()
    {
        return $this->belongsTo(self::class, 'inviter_id');
    }

    public function favourGoods()
    {
        return $this->belongsToMany(Goods::class, 'user_favour_goods')->orderBy('user_favour_goods.created_at', 'desc');
    }

    public function isMyFavourGoods($goods_id)
    {
        return \DB::table('user_favour_goods')->where('user_id', $this->id)->where('goods_id', $goods_id)->first() ? true : false;
    }

    public function getLastMemberLevel($member_level_id)
    {
        return self::memberLevels()->where('member_level_id', $member_level_id)->orderBy(\DB::raw('if(isnull(`validity_ended_at`), "9999-12-12", `validity_ended_at`)'), 'desc')->first();
    }

    public function getAdminLinkAttribute()
    {
        if (! $this->id) {
            return '';
        }
        $route_name = 'admin::'.lcfirst(\Illuminate\Support\Str::plural((class_basename(get_called_class())))).'.show';
        if (app('router')->has($route_name)) {
            return route($route_name, ['id' => $this]);
        } else {
            return '';
        }
    }

    /**
     * 验证inviter_id是否合法
     * 本人不能邀请自己，id必须存在用户
     * @param $inviter_id
     * @return bool|User
     * @author klinson <klinson@163.com>
     */
    public static function checkInviter($inviter_id = 0)
    {
        $inviter_id = intval($inviter_id);
        if (empty($inviter_id)) return false;

        if (\Auth::check() && $inviter_id == \Auth::user()->id) {
            return false;
        }
        if ($inviter = self::find($inviter_id)) {
            return $inviter;
        } else {
            return false;
        }
    }

    /**
     * 获取inviters
     * @param array $inviter_ids
     * @return \Illuminate\Support\Collection
     * @author klinson <klinson@163.com>
     */
    public static function getInviters($inviter_ids = [])
    {
        if (empty($inviter_ids)) return collect([]);

        if (\Auth::check() && ($key = array_search(\Auth::user()->id, $inviter_ids)) !== false) {
            unset($inviter_ids[$key]);
        }

        if (empty($inviter_ids)) return collect([]);

        return User::whereIn('id', $inviter_ids)->get();
    }
}
