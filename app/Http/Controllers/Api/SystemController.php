<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-5-31
 * Time: 下午1:24
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function getConfig(Request $request)
    {
        switch ($request->key) {
            case 'express_address':
                $return = config('system.express_address', []);
                break;
            default:
                $return = [];
                break;
        }

        return $this->response->array($return);
    }
}
