<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

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
        $chance = LotteryChance::getMyChance();
        if (empty($chance)) {
            return $this->response->errorBadRequest('您的抽奖次数已经用完');
        }
        DB::beginTransaction();

        try {
            $chance->setUsed();
            $prize = Prize::lottery();
//            $prize = null;
            if ($prize) {
                // 中奖
                $record = LotteryRecord::generateRecord(\Auth::user(), $prize, $chance);
            }

            DB::commit();

            if (isset($record) && !empty($record)) {
                return $this->response->item($record, new LotteryRecordTransformer());
            } else {
                return $this->response->noContent();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response->errorBadRequest('抽奖失败，' . $exception->getMessage());
        }
    }

    // 我的抽奖机会次数
    public function myChanceCount()
    {
        return $this->response->array([
            'count' => LotteryChance::getUnusedCount(\Auth::user()->id)
        ]);
    }


}