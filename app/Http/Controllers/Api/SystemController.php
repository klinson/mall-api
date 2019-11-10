<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 19-5-31
 * Time: 下午1:24
 */

namespace App\Http\Controllers\Api;


use App\Models\Article;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function getConfig(Request $request)
    {
        if ($request->key) {
            $keys = [$request->key];
        } else {
            $keys = is_array($request->keys) ? $request->keys : explode(',', $request->keys);
        }
        $return = [];
        foreach ($keys as $key) {
            switch ($key) {
                // 退货地址
                case 'express_address':
                    $return[$key] = config('system.express_address', []);
                    break;
                case 'enabled_lottery':
                    $return[$key] = ['status' => intval(config('system.enabled_lottery', 1))];
                    break;
                // 资讯（关于我们，入驻我们）
                case 'articles.about_us':
                case 'articles.join_us':
                case 'articles.lottery_intro':
                    $id = config('system.'.$key, 0);
                    $return[$key] = ['content' => ''];
                    if ($id && $article = Article::find($id)) {
                        $return[$key]['content'] = $article->content;
                    }
                    break;
                default:
                    break;
            }
        }

        if ($return) {
            if (count($keys) === 1) {
                $return = $return[$keys[0]];
            }
        }


        return $this->response->array($return);
    }
}
