<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Goods;
use App\Models\LotteryChance;
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
        return $this->response->item($goods, new GoodsTransformer('show'));
    }

    public function favour(Goods $goods)
    {
        \Auth::user()->favourGoods()->syncWithoutDetaching([
            $goods->id => [
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);

        LotteryChance::whenFavourGoods(\Auth::user());
        return $this->response->noContent();
    }

    public function unfavour(Request $request)
    {

        \Auth::user()->favourGoods()->detach($request->goods_ids);

        return $this->response->noContent();
    }

    public function favours(Request $request)
    {

        $list = \Auth::user()->favourGoods()->enabled()->paginate($request->per_page);


        return $this->response->paginator($list, new GoodsTransformer());
    }

    // 生成二维码
    public function qrcode(Goods $goods)
    {
        if ($goods->has_enabled != 1) {
            return $this->response->errorBadRequest('当前商品已下架');
        }

        $disk = 'qrcode';
        $user_id = \Auth::user()->id ?? 0;

        $filename = "goods/{$goods->id}_{$user_id}.png";

        try {
            if (! \Storage::disk($disk)->exists($filename)) {
                $scene = "g={$goods->id}&iu={$user_id}";

                $stream = app('wechat.mini_program')->app_code->getUnlimit($scene, [
                    'width' => 430,
                    'page' => 'pages/goodsDetail/goodsDetail'
                ]);
                if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                    // 以内容 md5 为文件名存到本地
                    //      $stream->save('abc');
                    // 自定义文件名，不需要带后缀
                    //      $stream->saveAs('abc', 'aaa');

                    \Storage::disk($disk)->put($filename, $stream);
                } else {
                    $msg = "生成小程序码失败，请稍后重试。";
                    if (isset($stream['errcode'])) {
                        $msg .= "{$stream['errmsg']}[{$stream['errcode']}]";
                    }
                    return $this->response->errorBadRequest($msg);
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