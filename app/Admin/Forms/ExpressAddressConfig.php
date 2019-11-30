<?php

namespace App\Admin\Forms;

use App\Models\Express;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class ExpressAddressConfig extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '快递配置';

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
            'system|express_company_id',
            'system|express_address|name',
            'system|express_address|address',
            'system|express_address|mobile'
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
        // setElementClass只为避免bug
        $this->select('system|express_company_id', '默认寄件快递公司')->options(Express::all()->pluck('name', 'id')->toArray())->rules('required')->setElementClass('s')->help('系统使用发货功能时，默认首先快递公司');

        $this->text('system|express_address|name', '退货快递收件人')->rules('required');
        $this->text('system|express_address|mobile', '退货快递电话')->rules('required');
        $this->textarea('system|express_address|address', '退货快递地址')->rules('required');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'system|express_company_id' => config('system.express_company_id', 0),
            'system|express_address|name' => config('system.express_address.name', ''),
            'system|express_address|mobile' => config('system.express_address.mobile', ''),
            'system|express_address|address' => config('system.express_address.address', ''),
        ];
    }
}
