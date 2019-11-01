<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\MemberRechargeActivity;
use App\Transformers\MemberRechargeActivityTransformer;

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

}