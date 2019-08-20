<?php

namespace App\Models;

use App\Models\Traits\IntTimestampsHelper;
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
        'nickname', 'sex', 'wxapp_openid', 'mobile', 'has_enabled', 'avatar', 'wxapp_userinfo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

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
}
