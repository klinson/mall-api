<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User as Model;

class UserTransformer extends TransformerAbstract
{
    protected $token;

    protected $availableIncludes = ['agency', 'validMemberLevels'];

    public function __construct($token = '')
    {
        $this->token = $token;
    }

    public function transform(Model $model)
    {
        $avatar = $model->avatar ?: asset('/images/avatar_'.$model->sex.'.png');
        switch ($this->token) {
            case 'info':
                return [
                    'id' => $model->id,
                    'wxapp_openid' => $model->wxapp_openid,
                    'nickname' => $model->nickname,
                    'mobile' => $model->mobile,
                    'sex' => $model->sex,
                    'agency_id' => $model->agency_id,
                    'avatar' => $avatar,
                    'created_at' => $model->created_at->toDateTimeString(),
                ];
                break;
            case '':
                return [
                    'id' => $model->id,
                    'nickname' => $model->nickname,
                    'sex' => $model->sex,
                    'avatar' => $avatar,
                ];
                break;
            default:
                return [
                    'user' => [
                        'id' => $model->id,
                        'wxapp_openid' => $model->wxapp_openid,
                        'nickname' => $model->nickname,
                        'mobile' => $model->mobile,
                        'sex' => $model->sex,
                        'agency_id' => $model->agency_id,
                        'avatar' => $avatar,
                        'created_at' => $model->created_at->toDateTimeString(),
                    ],
                    'token' => $this->token,
                ];
                break;
        }
    }

    public function includeAgency(Model $model)
    {
        if ($model->agency) {
            return $this->item($model->agency, new AgencyConfigTransformer());
        } else {
            return null;
        }
    }

    public function includeValidMemberLevels(Model $model)
    {
        return $this->collection($model->validMemberLevels, new UserHasMemberLevelTransformer());
    }

}