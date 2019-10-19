<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Transformers\GoodsTransformer;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function index(Request $request)
    {
        $query = Goods::query();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if (! blank($request->q)) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->has_recommended == 1) {
            $query = $query->where('has_recommended', 1);
        }

        $list = $query->enabled()->sort()->ById()->paginate($request->per_page);

        return $this->response->paginator($list, new GoodsTransformer());

    }

    public function show(Goods $goods)
    {
        if ($goods->has_enabled != 1) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($goods, new GoodsTransformer('show'));
    }

    // 生成二维码
    public function qrcode(Goods $goods)
    {
        if ($goods->has_enabled != 1) {
            return $this->response->errorNotFound();
        }

        $disk = 'qrcode';
        $user_id = \Auth::user()->id ?? 0;

        $filename = "goods/{$goods->id}_{$user_id}.png";

        try {
            if (! \Storage::disk($disk)->exists($filename)) {
                $scene = "goods_id={$goods->id}&inviter_id={$user_id}";

                $stream = app('wechat.mini_program')->app_code->getUnlimit($scene, ['width' => 430]);
                if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                    // 以内容 md5 为文件名存到本地
                    //      $stream->save('abc');
                    // 自定义文件名，不需要带后缀
                    //      $stream->saveAs('abc', 'aaa');

                    \Storage::disk($disk)->put($filename, $stream);
                }

            }

            return $this->response->array([
                'url' => \Storage::disk($disk)->url($filename)
            ]);
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest('生成小程序码失败，请稍后重试');
        }

    }
}