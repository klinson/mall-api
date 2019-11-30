<?php

namespace App\Admin\Forms;

use App\Models\Express;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class SystemConfig extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '系统配置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());
        $data = $request->only([
            'system|order_auto_receive_days',
            'system|order_cannot_refund_days',
            'system|order_auto_settle_days',
            'system|invite_bonus_rate',
        ]);

        foreach ($data as $key => $datum) {
            $key = strtr($key, '|', '.');

            update_config($key, $datum);
        }

        admin_success('操作成功');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->number('system|order_auto_receive_days', '自动确认期限（天）')->required()->min(0)->help('订单系统发货后N天自动确认到货')->setElementClass('s2');
        $this->number('system|order_cannot_refund_days', '退款期限（天）')->required()->min(0)->help('订单到货后N天后不能发起退货退款')->setElementClass('s2');
        $this->number('system|order_auto_settle_days', '结算时限（天）')->required()->min(0)->help('订单到货后N天后自动结算到邀请人的金库')->setElementClass('s2');
        $this->number('system|invite_bonus_rate', '邀请佣金比例')->required()->min(0)->help('邀请购买佣金比例, 1=>0.01%,500=>5%,10000=>100%')->setElementClass('s2');


    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'system|order_auto_receive_days' => config('system.order_auto_receive_days', 7),
            'system|order_cannot_refund_days' => config('system.order_cannot_refund_days', 7),
            'system|order_auto_settle_days' => config('system.order_auto_settle_days', 7),
            'system|invite_bonus_rate' => config('system.invite_bonus_rate', 0),
        ];
    }
}
