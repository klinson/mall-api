<?php
/**
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2019/11/21
 * Time: 9:34
 */

namespace App\Admin\Controllers\Traits;


use App\Admin\Extensions\Tools\DefaultBatchTool;
use Illuminate\Http\Request;

/**
 * 批操作助手
 * 用于批量审核、取消等操作所使用
 * 操作ids，数组格式
 *
 * 路由文件加入以下路由
$router->put('orders/batch/{handle}', 'OrdersController@batch')->where('handle', 'pass|reject');

 * grid 加入批操作按钮
$grid->tools(function (Grid\Tools $tools) {
    $tools->batch(function (Grid\Tools\BatchActions $batch) {
        $this->generateBatchPatchButton($batch);
        //$batch->add('批量审核通过', new DefaultBatchTool('batch/pass'));
        //$batch->add('批量审核不通过', new DefaultBatchTool('batch/reject'));
    });
});

 * Trait HasBatchHandle
 * @package App\Admin\Controllers\Traits
 */
trait HasBatchHandle
{
    /*
     * 注释掉是为了方便继承此trait直接定义参数而不会冲突
    // 可支持的批操作
    protected $allowBatchHandle = [];
    // 批操作名称，不存在则采用默认
    protected $batchHandle2name = [];
    protected $batchHandleDefaultName = '操作';
    // 批操作后单个信息打印字段列表，用' | '分割
    protected $batchTipFields = [];
    // 出现异常是否提示不要反复尝试
    protected $batchHandleErrorTipDontTry = true;
    protected $batchHandleErrorTip = '存在异常，请勿重试，请联系管理员';
    // 操作model的类 如App\Models\User::class
    protected $batchHandleModelClass;
    */

    // 批操作
    public function batch(Request $request, $handle)
    {
        if (empty($this->allowBatchHandle) || ! in_array($handle, $this->allowBatchHandle)) {
            $data = [
                'status'  => false,
                'message' => '不存在此操作',
            ];
            return response()->json($data);
        }

        $list = $this->batchHandleModelClass::whereIn('id', $request->ids)->get();
        if ($list->isEmpty()) {
            $data = [
                'status'  => false,
                'message' => '请选择数据',
            ];
            return response()->json($data);
        }

        $handle_name = $this->getBatchHandleName($handle);
        $info = [];
        $success_count = 0;
        $fail_count = 0;
        $error_count = 0;
        foreach ($list as $item) {
            $title = $this->generateBatchShowTitle($item);
            try {
                if ($item->$handle()) {
                    $success_count++;
                    $info[] = "{$title}：{$handle_name}  <span class='label label-success'>成功</span>";
                } else {
                    $fail_count++;
                    $info[] = "{$title}：{$handle_name}  <span class='label label-warning'>失败</span>";
                }
            } catch (\Exception $exception) {
                $error_count++;
                $info[] = "{$title}：{$handle_name}  <span class='label label-danger'>异常</span>  ：". $exception->getMessage() . '['.$exception->getCode().']';
            }
        }

        return $this->showResult($handle_name, $info, $success_count, $fail_count, $error_count);
    }

    // 生成单条的打印信息投
    protected function generateBatchShowTitle($item)
    {
        $title = '#'.$item->id;
        if (! empty($this->batchTipFields)) {
            $shows = [];
            foreach ($this->batchTipFields as $field) {
                $shows[] = $item->$field;
            }
            $title = $title . '（' . implode(' | ', $shows) . '）';
        }

        return $title;
    }

    // 获取批操作名称
    protected function getBatchHandleName($handle)
    {
        return isset($this->batchHandle2name[$handle]) ? $this->batchHandle2name[$handle] : ($this->batchHandleDefaultName ?? '操作');
    }

    // 输出结果
    protected function showResult($handle_name, $info = [], $success_count = 0, $fail_count = 0, $error_count = 0)
    {
        $tip_title = sprintf('%s完成，有%d条成功，%d条失败，%d异常。', $handle_name, $success_count, $fail_count, $error_count);
        if ($error_count) {
            $tip_function = 'admin_error';
            // 默认提醒不要重复操作
            if (! isset($this->batchHandleErrorTipDontTry)) {
                $this->batchHandleErrorTipDontTry = true;
            }
            if ($this->batchHandleErrorTipDontTry !== false) {
                $tip_title .= ($this->batchHandleErrorTip ?? '存在异常，请勿重试，请联系管理员');
            }
        } elseif ($fail_count) {
            $tip_function = 'admin_warning';
        } else {
            $tip_function = 'admin_success';
        }
        $tip_function($tip_title, implode("<br/>", $info));
        $data = [
            'status'  => true,
            'message' => $handle_name.'完成',
        ];
        return response()->json($data);
    }

    // 快速生成已经定义允许的批操作按钮
    protected function generateBatchPatchButton($batch)
    {
        foreach ($this->allowBatchHandle as $handle) {
            $batch->add('批量'.$this->getBatchHandleName($handle), new DefaultBatchTool('batch/'.$handle));
        }
        return $batch;
    }
}
