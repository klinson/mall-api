<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\AgencyConfig;
use App\Transformers\AgencyConfigTransformer;
use Illuminate\Http\Request;
use Auth;

class AgencyController extends Controller
{
    public function agencyConfigs()
    {
        $configs = AgencyConfig::all();
        return $this->response->collection($configs, new AgencyConfigTransformer());
    }

}