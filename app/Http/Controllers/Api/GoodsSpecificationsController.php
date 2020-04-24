<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\GoodsSpecification;
use App\Rules\CnMobile;
use App\Transformers\AddressTransformer;
use App\Transformers\GoodsSpecificationTransformer;
use Illuminate\Http\Request;

class GoodsSpecificationsController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsSpecification::query();
        if ($request->ids) {
            if (! is_array($request->ids)) {
                $request->ids = explode(',', $request->ids);
            }
            $query->whereIn('id', $request->ids);

            if ($request->sort_by_key) {
                $query->orderByRaw('field(`id`, '.implode(',', $request->ids).')');
            }

        } else if ($request->id) {
            $query->where('id', $request->id);
        } else {
            return $this->response->noContent();
        }


        $list = $query->get();
        return $this->response->collection($list, new GoodsSpecificationTransformer());
    }


}