<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Handlers\SmsHandler;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Auth;
class UserController extends Controller
{
    public function show()
    {
        return $this->response->item($this->user, new UserTransformer('info'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required',
            'sex' => 'required',
            'avatar' => 'required',
        ], [], [
            'nickname' => '昵称',
            'sex' => '性别',
            'avatar' => '头像',
        ]);

        Auth::user()->fill($request->only(['nickname', 'sex', 'avatar']));
        Auth::user()->save();
        return $this->response->item(Auth::user(), new UserTransformer('info'));
    }



    /**
     * 获取手机验证码
     * @param Request $request
     * @throws \Exception
     * @author klinson <klinson@163.com>
     */
    public function getMobileCode(Request $request)
    {
        // 掩耳盗铃的校验码
        if (strpos($request->check_code, 'klinson_') !== 0) return $this->response->errorBadRequest('校验码错误');

        if (blank($request->mobile)) {
            return $this->response->errorBadRequest('手机号不能为空');
        }
        if (! preg_match('/^1[0-9]{10}$/', $request->mobile)) {
            return $this->response->errorBadRequest('请输入正确手机号');
        }

        $user = User::where('mobile', $request->mobile)->first();
        if (! empty($user)) {
            return $this->response->errorBadRequest('该手机号已注册');
        }

        $code = cache('mobile_code:'.$request->mobile);
        if (! empty($code) && ($code['send_at'] + 60) > time()) {
            return $this->response->errorBadRequest('请勿频繁发送');
        }

        $code = random_string(4);
        cache(['mobile_code:'.$request->mobile => [
            'code' => $code,
            'send_at' => time()
        ]], 5);

        SmsHandler::getInstance()->sendVerifyCode($request->mobile, $code);

        return $this->response->array([
            'mobile' => $request->mobile,
        ]);
    }

    /**
     * 手机绑定/手机重绑
     * @param Request $request
     * @return \Dingo\Api\Http\Response|void
     * @throws \Exception
     * @author klinson <klinson@163.com>
     */
    public function bindMobile(Request $request)
    {
        if (blank($request->mobile)) {
            return $this->response->errorBadRequest('手机号不能为空');
        }
        if (blank($request->code)) {
            return $this->response->errorBadRequest('验证码不能为空');
        }
        if (! preg_match('/^1[0-9]{10}$/', $request->mobile)) {
            return $this->response->errorBadRequest('请输入正确手机号');
        }

        $user = User::where('mobile', $request->mobile)->first();
        if (! empty($user)) {
            return $this->response->errorBadRequest('该手机号已注册');
        }
        $code = cache('mobile_code:'.$request->mobile);
        if (empty($code) || ($code['send_at'] + 60 * 5) < time() || $code['code'] !== $request->code) {
            return $this->response->errorBadRequest('手机验证码错误');
        }

        \Auth::user()->mobile = $request->mobile;
        \Auth::user()->save();

        return $this->response->item(\Auth::user(), new UserTransformer());

    }
}