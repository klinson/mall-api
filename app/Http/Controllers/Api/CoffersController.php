<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\CofferWithdrawal;
use App\Transformers\CofferLogTransformer;
use App\Transformers\CofferTransformer;
use App\Transformers\CofferWithdrawalTransformer;
use Auth;
use function EasyWeChat\Kernel\Support\get_client_ip;
use Illuminate\Http\Request;

class CoffersController extends Controller
{
    public function __construct()
    {
        $this->authorize('is-agency');
    }

    public function show()
    {
        return $this->response->item($this->user->coffer, new CofferTransformer());
    }

    public function logs(Request $request)
    {
        $logs = $this->user->coffer->logs()->recent()->paginate($request->per_page);
        return $this->response->paginator($logs, new CofferLogTransformer());
    }

    // 金库提现申请
    public function withdraw(Request $request)
    {
        $balance = intval($request->balance);
        if (! $balance) {
            return $this->response->errorBadRequest('提现金额不能为空');
        }

        if ($this->user->coffer->balance < $balance) {
//            return $this->response->errorBadRequest('提现金额不能大于金库可提现金额');
        }

        $withdrawal = $this->user->coffer->withdrawals()->create([
            'balance' => $balance,
            'status' => 1,
            'ip' => ip2long(get_client_ip()),
        ]);

        return $this->response->item($withdrawal, new CofferWithdrawalTransformer());
    }

    public function withdrawals(Request $request)
    {
        $logs = $this->user->coffer->withdrawals()->recent()->paginate($request->per_page);
        return $this->response->paginator($logs, new CofferWithdrawalTransformer());
    }

    public function withdrawal(CofferWithdrawal $withdrawal)
    {
        $this->authorize('is-mine', $withdrawal);

        return $this->response->item($withdrawal, new CofferWithdrawalTransformer());
    }
}