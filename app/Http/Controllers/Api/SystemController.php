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
        switch ($request->key) {
            // 退货地址
            case 'express_address':
                $return = config('system.express_address', []);
                break;
            // 资讯（关于我们，入驻我们）
            case 'articles.about_us':
            case 'articles.join_us':
            case 'articles.lottery_intro':
                $id = config('system.'.$request->key, 0);
                $return = ['content' => ''];
                if ($id && $article = Article::find($id)) {
                    $return['content'] = $article->content;
                }
                break;
            default:
                $return = [];
                break;
        }

        return $this->response->array($return);
    }
}
