<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-5-31
 * Time: 下午1:24
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Storage;

class FilesController extends Controller
{
    public function upload(Request $request)
    {
        $return['path'] = $request->file('file')->store(date('Ymd'), 'wechat_upload');
        $return['url'] = Storage::disk('wechat_upload')->url($return['path']);
        return $this->response->array($return);
    }
}
