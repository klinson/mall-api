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
            'system|enabled_lottery',
            'system|non_prize_rate',
        ]);

        foreach ($data as $key => $datum) {
            if ($key === 'system|enabled_lottery') {
                $datum = $datum === 'on' ? 1 : 0;
            }
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
        $states = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $this->switch('system|enabled_lottery', '启用抽奖?')->states($states)->help('关闭时，小程序首页抽奖入口隐藏，禁止抽奖，可查看抽中奖品')->setElementClass('s1');
        $this->number('system|non_prize_rate', '【谢谢参与】权值')->required()->min(0)->help('【谢谢参与】的概率=【谢谢参与】权值/（所有奖品总权值+【谢谢参与】权值）')->setElementClass('s2');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'system|enabled_lottery' => config('system.enabled_lottery', 1),
            'system|non_prize_rate' => config('system.non_prize_rate', 0),
        ];
    }
}
