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
        $broadcast =  Broadcast::getShow();
        if ($broadcast) {
            return $this->response->item($broadcast, new BroadcastTransformer());
        } else {
            return $this->response->noContent();
        }
    }
}