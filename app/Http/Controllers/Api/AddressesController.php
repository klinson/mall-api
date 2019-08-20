<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Transformers\AddressTransformer;

class AddressesController extends Controller
{
    public function index()
    {
        return $this->response->collection($this->user->addresses, new AddressTransformer());
    }
}