<?php

namespace App\Admin\Forms;

use App\Models\Express;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class IntegralConfig extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '积分汇率';

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
            'system|integral2money_rate',
            'system|money2integral_rate',
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
        $this->number('system|integral2money_rate', '积分_to_钱')->required()->min(0)->help('0.01=>100积分*0.01等于1块钱')->setElementClass('s2');
        $this->number('system|money2integral_rate', '消费金额_to_积分')->required()->min(0)->help('1 => 1块钱*1等于1积分')->setElementClass('s2');

    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'system|integral2money_rate' => config('system.integral2money_rate', 0.01),
            'system|money2integral_rate' => config('system.money2integral_rate', 1),
        ];
    }
}
