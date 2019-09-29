<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Transformers\GoodsTransformer;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function index(Request $request)
    {
        $query = Goods::query();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if (! blank($request->q)) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->has_recommended == 1) {
            $query = $query->where('has_recommended', 1);
        }

        $list = $query->enabled()->sort()->ById()->paginate($request->per_page);

        return $this->response->paginator($list, new GoodsTransformer());

    }

    public function show(Goods $goods)
    {
        if ($goods->has_enabled != 1) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($goods, new GoodsTransformer('show'));
    }
}