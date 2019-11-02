<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\MemberLevel;
use App\Models\UserHasMemberLevel;
use App\Transformers\CategoryTransformer;
use App\Transformers\MemberLevelTransformer;
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

    public function activities()
    {
        return $this->response->collection(MemberLevel::enabled()->levelBy()->get(), new MemberLevelTransformer());

    }

    // 我目前拥有的会员
    public function mine()
    {
        return $this->response->collection(\Auth::user()->validMemberLevels, new UserHasMemberLevelTransformer());
    }
}