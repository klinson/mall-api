<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Broadcast;
use App\Transformers\BroadcastTransformer;

class BroadcastsController extends Controller
{
    public function show()
    {
        return $this->response->item(Broadcast::getShow(), new BroadcastTransformer());
    }
}