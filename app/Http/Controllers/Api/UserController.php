<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

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
}