<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Auth;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function wxappLogin(Request $request)
    {
        if (blank($request->code)) {
            return $this->response->errorBadRequest('code不能为空');
        }

        $app = app('wechat.mini_program');
        $wechat_info = $app->auth->session($request->code);
//        $wechat_info = [
//            'openid' => 2111111111,
//            'session_key' => 'SESSION_KEY'
//        ];
        if (isset($wechat_info['openid'])) {
            $user = User::where('wxapp_openid', $wechat_info['openid'])->first();

            if (empty($user)) {
                // 第一次登录
                if (! $request->user || ! is_array($request->user)) {
                    if ($request->type === 'try') {
                        // 静默尝试登录
                        return $this->response->array([
                            'wxapp_openid' => $wechat_info['openid'],
                        ]);
                    } else {
                        return $this->response->errorBadRequest('用户信息不能为空');
                    }
                }
                $info = $request->user;

                $user = new User([
                    'wxapp_openid' => $wechat_info['openid'],
                    'nickname' => $info['nickName'],
                    'sex' => $info['gender'],
                    'avatar' => $info['avatarUrl'],
                    'wxapp_userinfo' => json_encode($info),
                    'has_enabled' => 1,
                    'mobile' => '',
                ]);
                $user->save();
            } else {
                // 已绑定

            }
            $token = Auth::guard('api')->login($user);

            return $this->response->item($user, new UserTransformer($token));
        } else {
            return $this->response->errorBadRequest('登录失败，' . $wechat_info['errmsg']);
        }
    }

    public function login(AuthorizationsRequest $request)
    {
        //TODO: check user login

        // 验证规则，由于业务需求，这里我更改了一下登录的用户名，使用手机号码登录
        $rules = [

        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // 使用 Auth 登录用户，如果登录成功，则返回 201 的 code 和 token，如果登录失败则返回
        return ($token = Auth::guard('api')->attempt($params))
            ? $this->respondWithToken($token)->setStatusCode(201)
            : $this->response->errorUnauthorized(trans('auth.failed'));
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
