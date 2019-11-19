<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\MemberRechargeActivity;
use App\Models\MemberRechargeOrder;
use App\Models\User;
use App\Transformers\MemberRechargeActivityTransformer;
use App\Transformers\UserTransformer;
use DB;
use Illuminate\Http\Request;

class MemberRechargeActivitiesController extends Controller
{
    public function index()
    {
        return $this->response->collection(MemberRechargeActivity::enabled()->levelBy()->get(), new MemberRechargeActivityTransformer());
    }

    public function show(MemberRechargeActivity $activity)
    {
        return $this->response->item($activity, new MemberRechargeActivityTransformer());
    }

    // 生成二维码
    public function qrcode(MemberRechargeActivity $activity)
    {
        if ($activity->has_enabled != 1) {
            return $this->response->errorNotFound();
        }

        $disk = 'qrcode';
        $user_id = \Auth::user()->id ?? 0;

        $filename = "activity/{$activity->id}_{$user_id}.png";

        try {
            if (! \Storage::disk($disk)->exists($filename)) {
                $scene = "a={$activity->id}&iu={$user_id}";

                $stream = app('wechat.mini_program')->app_code->getUnlimit($scene, [
                    'width' => 430,
//                    'page' => 'pages/goodsDetail/goodsDetail'
                ]);
                if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                    // 以内容 md5 为文件名存到本地
                    //      $stream->save('abc');
                    // 自定义文件名，不需要带后缀
                    //      $stream->saveAs('abc', 'aaa');

                    \Storage::disk($disk)->put($filename, $stream);
                } else {
                    $msg = "生成小程序码失败，请稍后重试。";
                    if (isset($stream['errcode'])) {
                        $msg .= "{$stream['errmsg']}[{$stream['errcode']}]";
                    }
                    return $this->response->errorBadRequest($msg);
                }
            }

            return $this->response->array([
                'url' => \Storage::disk($disk)->url($filename)
            ]);
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest('生成小程序码失败，请稍后重试');
        }

    }

    // 邀请排行
    public function inviteRank(Request $request)
    {
        $count = $request->count ?: 10;
        $list = DB::table('coffer_logs')->where('type', 2)->where('data_type', MemberRechargeOrder::class)->selectRaw("`user_id`, sum(`balance`) as total_balance, count(*) as total_number")->groupBy('user_id')->orderBy('total_balance', 'desc')->limit($count)->get();

        $user_ids = $list->pluck('user_id')->toArray();
        $users = User::whereIn('id', $user_ids)->get()->keyBy('id');
        $list = $list->toArray();
        $transformer = new UserTransformer('hidden');
        $res = [];
        foreach ($list as $item) {
            if (isset($users[$item->user_id])) {
                $res[] = [
                    'user_id' => $item->user_id,
                    'total_balance' => $item->total_balance,
                    'total_number' => $item->total_number,
                    'user' => $transformer->transform($users[$item->user_id]),
                ];
            }
        }

        return $this->response->array($res);
    }

}