<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\MemberLevel;
use App\Transformers\MemberLevelTransformer;
use App\Transformers\MemberRechargeActivityTransformer;
use App\Transformers\UserHasMemberLevelTransformer;

class MemberLevelsController extends Controller
{
    public function index()
    {
        return $this->response->collection(MemberLevel::enabled()->levelBy()->get(), new MemberLevelTransformer());
    }

    public function show(MemberLevel $memberLevel)
    {
        return $this->response->item($memberLevel, new MemberLevelTransformer());
    }

    // 获取最大优惠的会员
    public function max()
    {
        $member = MemberLevel::enabled()->orderBy('discount', 'desc')->levelBy()->first();
        if (empty($member)) {
            return $this->response->noContent();
        }
        $activity = $member->activities()->orderBy('level', 'desc')->first();
        $res = [
            'member_level' => (new MemberLevelTransformer())->transform($member),
            'activity' => $activity ? (new MemberRechargeActivityTransformer())->transform($activity) : [],
        ];

        return $this->response->array($res);
    }

    public function activities()
    {
        return $this->response->collection(MemberLevel::enabled()->levelBy()->get(), new MemberLevelTransformer());

    }

    // 我目前拥有的会员
    public function mine()
    {
        return $this->response->collection(\Auth::user()->real_member_levels, new UserHasMemberLevelTransformer());
    }
}