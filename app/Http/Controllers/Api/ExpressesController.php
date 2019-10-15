<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Express;
use App\Transformers\ExpressTransformer;

class ExpressesController extends Controller
{
    public function index()
    {
        $list =  Express::enabled()->sort()->get();
        return $this->response->collection($list, new ExpressTransformer());
    }
}