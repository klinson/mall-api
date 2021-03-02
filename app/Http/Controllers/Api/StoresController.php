<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Http\Request;

class StoresController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::getByCache();
        if ($request->latitude && $request->longitude) {
            foreach ($stores as &$store) {
                $store['distance'] = get_distance($request->latitude, $request->longitude, $store['latitude'], $store['longitude']);
            }
            $stores = array_values(array_sort($stores, 'distance'));
        }
        return $this->response->array($stores);
    }


}