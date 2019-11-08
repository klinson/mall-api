<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Jobs\RecordLotteryJob;
use App\Models\LotteryChance;
use App\Models\LotteryRecord;
use App\Models\Prize;
use App\Models\User;
use App\Transformers\LotteryRecordTransformer;
use App\Transformers\PrizeTransformer;
use DB;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    public function prizes()
    {
        $prizes = Prize::enabled()->levelBy()->ById()->get();

        return $this->response->collection($prizes, new PrizeTransformer());
    }

    // 赠送抽奖（生产环境不可用，便于测试)
    public function presentChance(Request $request)
    {
        if (! \App::environment(['local', 'dev', 'development'])) {
            return $this->response->errorBadRequest('当前环境不支持');
        }

        if (empty($request->user_id) || ! $user = User::find($request->user_id)) {
            return $this->response->errorBadRequest('未选择赠送指定人');
        }

        $count = $request->count ?: 1;

        LotteryChance::present($user, $count);

        return $this->response->noContent();
    }


    // 抽奖
    public function lottery()
    {
        $user_id = \Auth::user()->id;
        if (LotteryChance::getUnusedCountByCache($user_id) <= 0) {
            return $this->response->errorBadRequest('您的抽奖次数已经用完');
        }
        if (! LotteryChance::useOne($user_id)) {
            return $this->response->errorBadRequest('您的抽奖次数已经用完');
        }

        try {
            $prize = Prize::lottery();

            if (!empty($prize)) {
                $this->dispatch(new RecordLotteryJob($user_id, $prize));

                return $this->response->item($prize, new PrizeTransformer());
            } else {
                return $this->response->noContent();
            }
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest('抽奖失败，' . $exception->getMessage());
        }
    }

    // 我的抽奖机会次数
    public function myChanceCount()
    {
        $count = LotteryChance::getUnusedCountByCache(\Auth::user()->id);
        return $this->response->array([
            'count' => $count > 0 ? $count : 0
        ]);
    }


}