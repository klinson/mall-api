<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Prize;
use App\Transformers\PrizeTransformer;

class LotteryController extends Controller
{
    public function prizes()
    {
        $prizes = Prize::enabled()->levelBy()->ById()->get();

        return $this->response->collection($prizes, new PrizeTransformer());
    }

}