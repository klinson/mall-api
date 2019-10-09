<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Transformers\WalletTransformer;
use Auth;

class WalletsController extends Controller
{
    public function show()
    {
        return $this->response->item($this->user->wallet, new WalletTransformer());
    }

}