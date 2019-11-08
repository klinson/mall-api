<?php

namespace App\Admin\Forms;

use App\Models\Express;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class LotteryConfig extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '抽奖配置';

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
            'system|non_prize_rate',
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
        $this->number('system|non_prize_rate', '【谢谢参与】权值')->rules(['required|min:0'])->setElementClass('s')->help('【谢谢参与】的概率=【谢谢参与】权值/（所有奖品总权值+【谢谢参与】权值）');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'system|non_prize_rate' => config('system.non_prize_rate', 0),
        ];
    }
}
