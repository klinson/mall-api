<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2018/10/17
 * Time: 23:07
 */

namespace App\Admin\Controllers;

use App\Admin\Forms\ExpressAddressConfig;
use App\Admin\Forms\LotteryConfig;
use App\Admin\Forms\SystemConfig;
use Encore\Admin\Config\ConfigModel;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    protected $pageHeader = '系统配置';

    public function index(Content $content)
    {

        $this->_setPageDefault($content);


        $forms = [
            'system' => SystemConfig::class,
            'express' => ExpressAddressConfig::class,
            'lottery' => LotteryConfig::class,
        ];


        return $content->body(Tab::forms($forms));
    }

    public function store(Request $request)
    {
        $config = ConfigModel::where('name', 'wxapp_about_us')->first();
        if (! empty($config)) {
            $config->value = $request->get('content', '');
            $config->save();
        }
        return redirect()->back();
    }
}