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


    protected $casts = [
        'wxapp_userinfo' => 'array'
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

    public function walletLogs()
    {
        return $this->hasMany(WalletLog::class);
    }

    public function integral()
    {
        return $this->hasOne(Integral::class, 'user_id', 'id');
    }

    public function integralLogs()
    {
        return $this->hasMany(IntegralLog::class);
    }

    public function coffer()
    {
        return $this->hasOne(Coffer::class, 'user_id', 'id');
    }

    public function cofferLogs()
    {
        return $this->hasMany(CofferLog::class);
    }

    public function init()
    {
        if (! $this->wallet) {
            $this->wallet()->create();
        }
        if (! $this->coffer) {
            $this->coffer()->create();
        }
        if (! $this->integral) {
            $this->integral()->create();
        }
        // 会员经验值
        if (! $this->score) {
            $this->scoreInit();
        }
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
    }

    // 会员初始化
    public function memberInit()
    {
    }

    // 会员经验值初始化
    public function scoreInit()
    {
        if (! $this->score) {
            UserScore::create([
                'user_id' => $this->id,
                'member_level_id' => 1,
                'balance' => 0,
            ]);
        }
    }

    // 会员经验值
    public function score()
    {
        return $this->hasOne(UserScore::class, 'user_id', 'id');
    }

    public function scoreLogs()
    {
        return $this->hasMany(UserScoreLog::class);
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

    /**
     * book分支下经验 会员优惠获取
     * @return int
     * @author klinson <klinson@163.com>
     */
    public function getScoreMemberDiscount()
    {
        return $this->score->memberLevel->discount ?? 100;
    }
    /**
     * book分支下经验 会员是否包邮
     * @return int
     * @author klinson <klinson@163.com>
     */
    public function hasFeeFreightByScoreMember()
    {
        return $this->score->memberLevel->is_fee_freight ?? 0;
    }

    // 获取用户当前会员最佳折扣
    public function getBestMemberDiscount($reset = false)
    {
        // book分支
        return $this->getScoreMemberDiscount();

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
        // book分支
        return $this->hasFeeFreightByScoreMember();

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

    public function isMyFavourGoods($goods)
    {
        return \DB::table('user_favour_goods')->where('user_id', $this->id)->where('goods_id', $goods->id)->where('goods_type', get_class($goods))->first() ? true : false;
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

    public function coupons()
    {
        return $this->hasMany(UserHasCoupon::class);
    }

    /**
     * 是否是员工
     * @return bool
     * @author klinson <klinson@163.com>
     */
    public function isStaff()
    {
        return $this->is_staff === 1;
    }

    /**
     * 生成后台form的模型select选择器
     * @param \Encore\Admin\Form $form
     * @param string $formField 存储表单字段
     * @param string $titles 选择下拉显示标题的字段，可以是title或者数组['id', 'title']或者id,title 多个会以|拼接
     * @param string $label 选择项目标题
     * @param boolean $is_all_options 是否一次获取全部
     * @param string $query_field 模糊查询字段
     * @param string $select_type 选择类型，可选参数select,multipleSelect
     * @author klinson <klinson@163.com>
     * @return $this|mixed
     */
    public static function form_display_select($form, $formField = '', $titles = 'title', $label = '', $is_all_options = true, $query_fields = 'title', $select_type = 'select')
    {
        if (empty($formField)) {
            $formField = \Illuminate\Support\Str::snake(class_basename(get_called_class()), '_').'_id';
        }
        if (empty($label)) {
            $label = __(ucfirst(\Illuminate\Support\Str::snake(class_basename(get_called_class()), ' ') . ' id'));
        }
        if (! is_array($titles)) {
            $titles = explode(',', $titles);
        }

        if (count($titles) == 1) {
            if (! $is_all_options) {
                return $form->$select_type($formField, $label)->match(function ($keyword) use ($query_fields, $titles) {
                    $query = static::query();
                    if ($keyword) {
                        $query_fields = explode(',', $query_fields);
                        if (count($query_fields) == 1) {
                            $query->where($query_fields[0], 'LIKE', '%' . $keyword . '%');
                        } else {
                            $query->where(function ($query) use ($query_fields, $keyword) {
                                foreach ($query_fields as $field) {
                                    $query->orWhere($field, 'LIKE', '%' . $keyword . '%');
                                }
                            });
                        }
                    }

                    return $query
                        // because select2 js plugin needs `text` and `id` column,
                        // so if your model does not contains these two, remember to AS for them
                        ->select([\DB::raw($titles[0].' AS text'), 'id'])
                        ->latest();
                })->text(function ($id) use ($titles) {
                    if (is_array($id)) {
                        return static::whereIn('id', $id)->select([\DB::raw($titles[0].' AS text'), 'id'])->pluck('text', 'id');
                    } else {
                        return static::where('id', $id)->select([\DB::raw($titles[0].' AS text'), 'id'])->pluck('text', 'id');
                    }
                    // return type is `{id1: text1, id2: text2...}
                });
            } else {
                return $form->$select_type($formField, $label)->options(static::all(['id', $titles[0]])->pluck($titles[0], 'id'));
            }
        } else {
            $selects = implode($titles, "`, ' | ', `");
            $selects = "concat(`{$selects}`) AS text";
            if (! $is_all_options) {
                return $form->$select_type($formField, $label)->match(function ($keyword) use ($query_fields, $selects) {
                    if ($keyword) {
                        $query = static::query();

                        $query_fields = explode(',', $query_fields);
                        if (count($query_fields) == 1) {
                            $query->where($query_fields[0], 'LIKE', '%' . $keyword . '%');
                        } else {
                            $query->where(function ($query) use ($query_fields, $keyword) {
                                foreach ($query_fields as $field) {
                                    $query->orWhere($field, 'LIKE', '%' . $keyword . '%');
                                }
                            });
                        }
                    }

                    return $query
                        // because select2 js plugin needs `text` and `id` column,
                        // so if your model does not contains these two, remember to AS for them
                        ->select([\DB::raw($selects), 'id'])
                        ->latest();
                })->text(function ($id) use ($selects) {
                    if (is_array($id)) {
                        return static::whereIn('id', $id)->select([\DB::raw($selects), 'id'])->pluck('text', 'id');
                    } else {
                        return static::where('id', $id)->select([\DB::raw($selects), 'id'])->pluck('text', 'id');
                    }
                    // return type is `{id1: text1, id2: text2...}
                });
            } else {
                $list = static::all([\DB::raw($selects), 'id'])->pluck('text', 'id');
                return $form->$select_type($formField, $label)->options($list);
            }
        }
    }

}
