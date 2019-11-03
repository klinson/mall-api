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
use App\Transformers\PrizeTransformer;
use DB;

class LotteryController extends Controller
{
    public function prizes()
    {
        $prizes = Prize::enabled()->levelBy()->ById()->get();

        return $this->response->collection($prizes, new PrizeTransformer());
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
//            $prize = Prize::lottery();
            $prize = null;
            if ($prize) {
                // 中奖
                LotteryRecord::generateRecord(\Auth::user(), $prize, $chance);
            }

            DB::commit();

            if ($prize) {
                return $this->response->item($prize, new PrizeTransformer());
            } else {
                return $this->response->noContent();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response->errorBadRequest('抽奖识别，' . $exception->getMessage());
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