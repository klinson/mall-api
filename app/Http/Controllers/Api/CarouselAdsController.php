<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\CarouselAd;
use Illuminate\Http\Request;

class CarouselAdsController extends Controller
{
    public function show(Request $request)
    {
        $ads =  CarouselAd::getByKeyByCache($request->key);
        if ($ads) {
            return $this->response->array($ads);
        } else {
            return $this->response->array([]);
        }
    }
}