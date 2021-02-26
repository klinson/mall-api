<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Transformers\IntegralLogTransformer;
use App\Transformers\IntegralTransformer;
use App\Transformers\WalletLogTransformer;
use Auth;
use Illuminate\Http\Request;

class IntegralsController extends Controller
{
    public function show()
    {
        return $this->response->item($this->user->integral, new IntegralTransformer());
    }

    public function logs(Request $request)
    {
        $logs = $this->user->integral->logs()->recent()->paginate($request->per_page);
        return $this->response->paginator($logs, new IntegralLogTransformer());
    }

}