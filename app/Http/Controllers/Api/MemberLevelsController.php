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
use App\Transformers\CategoryTransformer;
use App\Transformers\MemberLevelTransformer;

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
}