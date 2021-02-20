<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTransformer;

class CategoriesController extends Controller
{
    public function index()
    {
        $tree = Category::getByCache();
        return $this->response->array($tree);
    }

    // 推荐的分类
    public function top()
    {
        $list = Category::enabled()->orderBy('sort')->where('is_recommended', 1)->get();
        return $this->response->collection($list, new CategoryTransformer());
    }
}