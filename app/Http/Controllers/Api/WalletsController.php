<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\RechargeThresholdOrder;
use App\Transformers\RechargeThresholdOrderTransformer;
use App\Transformers\WalletLogTransformer;
use App\Transformers\WalletTransformer;
use Auth;
use Illuminate\Http\Request;

class WalletsController extends Controller
{
    public function show()
    {
        return $this->response->item($this->user->wallet, new WalletTransformer());
    }

    public function logs(Request $request)
    {
        $logs = $this->user->wallet->logs()->recent()->paginate($request->per_page);
        return $this->response->paginator($logs, new WalletLogTransformer());
    }

    // 钱包充值
    public function recharge(Request $request)
    {
        $balance = intval($request->balance);
        if (! $balance) {
            return $this->response->errorBadRequest('充值金额不能为空');
        }
        $order = RechargeThresholdOrder::generateRechargeOrder($this->user, $balance);

        if (app()->isLocal()) {
            $order->setSuccess();
            return $this->response->item($order, new RechargeThresholdOrderTransformer());
        } else {
            try {
                $config = $order->generatePayConfig();
                return $this->response->array($config);
            } catch (\Exception $exception) {
                return $this->response->errorBadRequest($exception->getMessage());
            }
        }
    }
}