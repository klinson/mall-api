<?php
/*
|--------------------------------------------------------------------------
| 自定义助手辅助函数
|--------------------------------------------------------------------------
*/

/**
 * 获取当前请求的控制器名和方法名
 * @author klinson <klinson@163.com>
 * @return array
 */
function getCurrentClassNameAndMethodName()
{
    $classMethod = request()->route()->getActionName();
    $tmp = explode('\\', $classMethod);
    $classMethod = array_pop($tmp);
    $tmp = explode('@', $classMethod);
    $tmp[0] = substr($tmp[0], 0, strlen($tmp[0])-10);

    return $tmp;
}

function list_to_tree($array, $root = 0, $id = 'id', $pid = 'pid', $child = 'child')
{
    $tree = [];
    foreach ($array as $k => $v) {
        if ($v[$pid] == $root) {
            $v[$child] = list_to_tree($array, $v[$id], $id, $pid, $child);
            $tree[] = $v;
            unset($array[$k]);
        }
    }
    return $tree;
}

function tree_to_list($tree, $id = 'id', $child = 'child')
{
    $array = array();
    foreach ($tree as $k => $val) {
        $array[] = $val;
        if (isset($val[$child])) {
            $children = tree_to_list($val[$child], $id, $child);
            if ($children) {
                $array = array_merge($array, $children);
            }
        }
    }
    foreach ($array as $key => $item) {
        unset($array[$key][$child]);
    }
    return $array;
}

/**
 * 后台自动上传的文件获取url
 * @param $path
 * @param string $server
 * @return mixed
 */
function get_admin_file_url($path, $server = '', $default = '')
{
    if (is_null($path) || $path === '') {
        return $default;
    }
    if (url()->isValidUrl($path)) {
        $src = $path;
    } elseif ($server) {
        $src = $server.$path;
    } else {
        $src = \Illuminate\Support\Facades\Storage::disk(config('admin.upload.disk'))->url($path);
    }
    return $src;
}

/**
 * 自动判断数组还是单个，获取url
 * @param $paths
 * @param string $server
 * @author klinson <klinson@163.com>
 * @return array|mixed
 */
function get_admin_file_urls($paths, $server = '')
{
    if (is_array($paths)) {
        $return = [];
        foreach ($paths as $path) {
            $return[] = get_admin_file_url($path, $server);
        }
        return $return;
    } else {
        return get_admin_file_url($paths, $server);
    }
}


function show_images($show, $column, $label = '', $server = '', $width = 200, $height = 200)
{
    $show->$column($label)->unescape()->as(function ($paths) use ($server, $width, $height) {
        $urls = get_admin_file_urls($paths, $server);
        if (empty($urls)) {
            return '';
        }
        if (is_string($urls)) {
            $urls = [$urls];
        }
        return implode("&nbsp;", array_map(function ($url) use ($width, $height) {
            return "<img src='$url' style='max-width:{$width}px;max-height:{$height}px' class='img img-thumbnail' />";
        }, $urls));
    });
}

/**
 * 下载微信临时资源
 * @param $media_id
 * @author klinson <klinson@163.com>
 * @return null
 */
function download_wechat_temp_media($media_id)
{
    $app = app('wechat.official_account');
    $stream = $app->media->get($media_id);

    if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
        // 以内容 md5 为文件名存到本地
//      $stream->save('abc');
        // 自定义文件名，不需要带后缀
//      $stream->saveAs('abc', 'aaa');

        // 获取文件名
        $h = $stream->getHeader('Content-disposition');
        $tmp = explode('=', $h[0]);
        $filename = trim($tmp[1], "\"'");

        if (! \Storage::disk('wechat')->exists($filename)) {
            \Storage::disk('wechat')->put($filename, $stream);
        }

        return \Storage::disk('wechat')->url($filename);
    }

    return null;
}

/**
 * 生成随机字符串
 * @param int $length 生成长度
 * @param int $type 字符串类型 0-7 8种模式
 * @author klinson <klinson@163.com>
 * @return string
 */
function random_string($length = 6, $type = 0): string
{
    $chars = [
        '0123456789',
        'abcdefghijklmnopqrstuvwxyz',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '!@#$%^&*()-_ []{}<>~`+=,.;:/?|'
    ];
    $char_seeder = '';
    switch ($type) {
        case 1:
            $char_seeder = $chars[1];
            break;
        case 2:
            $char_seeder = $chars[2];
            break;
        case 3:
            $char_seeder = $chars[3];
            break;
        case 4:
            $char_seeder = $chars[0] . $chars[1];
            break;
        case 5:
            $char_seeder = $chars[1] . $chars[2];
            break;
        case 6:
            $char_seeder = $chars[0] . $chars[1] . $chars[2];
            break;
        case 7:
            $char_seeder = $chars[0] . $chars[1] . $chars[2] . $chars[3];
            break;
        case 0:
        default:
            $char_seeder = $chars[0];
            break;
    }
    $random_string = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $random_string .= substr($char_seeder, mt_rand(0, strlen($char_seeder) - 1), 1);
        $random_string .= $char_seeder[ mt_rand(0, strlen($char_seeder) - 1) ];
    }

    return $random_string;
}

/**
 * 初始化自定义导出
 * @param $grid
 * @param $fields
 * @param $fileName
 * @param $transform
 * @author klinson <klinson@163.com>
 */
function gird_exporter_init(Encore\Admin\Grid $grid, $fields, $fileName, $transform = [])
{
    $grid->exporter((new \App\Admin\Extensions\Exporters\ExcelExporter())->setFields($fields)->setFileName($fileName)->setTransform($transform));
}

/**
 * 判断导航是否是当前页面打开的
 * @param $nav
 * @param string $url
 * @param string $children
 * @author klinson <klinson@163.com>
 * @return bool
 */
function check_nav_active($nav, $url = 'url', $children = 'children')
{
    $urls[] = $nav[$url] ?? '';
    if (isset($nav[$children]) && ! empty($nav[$children])) {
        $urls = array_merge($urls, array_column($nav['children'], $url));
    }
    $request_path = '/'.request()->decodedPath();

    foreach ($urls as $url_item) {
        if (\Illuminate\Support\Str::is($url_item, $request_path)) {
            return true;
        }
    }
    return false;
}

/**
 * 模型转后台链接显示
 * @param $item
 * @param string $title
 * @author klinson <klinson@163.com>
 * @return string
 */
function model2a($item, $title = 'title')
{
    if ($item->admin_link) {
        return "<a target='_blank' href='{$item->admin_link}'>{$item->$title}</a>";
    } else {
        return $item->$title;
    }
}

/**
 * @param \Encore\Admin\Grid $grid
 * @param string $related
 * @param string $title
 * @param null|string $relate_column
 * @author klinson <klinson@163.com>
 * @return mixed
 */
function grid_display_relation($grid, $related, $title = 'title', $relate_column = null)
{
    if (empty($relate_column)) {
        $relate_column = __(ucfirst(\Illuminate\Support\Str::snake($related, ' ')) . ' id');
    }
    return $grid->column($related, $relate_column)->display(function () use ($related, $title) {
        if (empty($this->$related)) {
            return '';
        }
        return model2a($this->$related, $title);
    });
}

/**
 * @param Encore\Admin\Show $show
 * @param string $related
 * @param string $title
 * @param null|string $relate_column
 * @author klinson <klinson@163.com>
 * @return mixed
 */
function show_display_relation($show, $related, $title = 'title', $relate_column = null)
{
    if (empty($relate_column)) {
        $relate_column = __(ucfirst(\Illuminate\Support\Str::snake($related, ' ')) . ' id');
    }
    return $show->field($related, $relate_column)->unescape()->as(function ($item) use ($title) {
        if (empty($item)) {
            return '';
        }
        return model2a($item, $title);
    });
}

/**
 * 微信支付md5 paySign
 * @param $appid
 * @param $nonceStr
 * @param $prepay_id
 * @param $timeStamp
 * @param $key
 * @author klinson <klinson@163.com>
 * @return string
 */
function generate_wechat_payment_md5_sign($appid, $nonceStr, $prepay_id, $timeStamp, $key)
{
    return md5("appId={$appid}&nonceStr=$nonceStr&package=prepay_id={$prepay_id}&signType=MD5&timeStamp={$timeStamp}&key={$key}");
}

/**
 * 记录邀请人
 * @param $model
 * @author klinson <klinson@163.com>
 * @return mixed
 */
function record_inviter($model)
{
    $model->inviter_id = request('inviter_id', 0);
    // 用户自身是代理则永远是自己邀请自己
    if (\Auth::check()) {
        if (\Auth::user()->agency_id) {
            $model->inviter_id = \Auth::user()->id;
        }
    }

    return $model;
}

/**
 * 显示批处理结果
 * @param int $error_count
 * @param array $info
 * @return \Illuminate\Http\JsonResponse
 * @author klinson <klinson@163.com>
 */
function show_batch_result($error_count = 0, $info = [])
{
    if ($error_count) {
        admin_warning("处理完成，存在{$error_count}条失败", implode("<br/>", $info));
    } else {
        admin_success('处理成功', implode("<br/>", $info));
    }

    $data = [
        'status'  => true,
        'message' => '操作完成',
    ];
    return response()->json($data);
}

/**
 * 批量或单个修改配置表
 * @param $key
 * @param null $value
 * @author klinson <klinson@163.com>
 */
function update_config($key, $value = null)
{
    if (is_array($key)) {
        foreach ($key as $k => $v) {
            update_config($k, $v);
        }
    } else {
        \App\Models\Config::updateOrCreate(
            ['name' => $key],
            ['value' => $value]
        );
    }
}

function grid_has_enabled($grid)
{
    $states = [
        'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
    ];
    $grid->column('has_enabled', __('Has enabled'))->switch($states)->filter(HAS_ENABLED2TEXT);
}

// int 转int 避免原来的int是float，直接转就精度丢失
// 如 100.0 => 100
function to_int($float)
{
    return intval(strval($float));
}