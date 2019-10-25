<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/17
 * Time: 23:52
 */

namespace App\Observers;

/**
 * 通用model
 * 自动观察器 批量操作无法触发观察器
 * 自动注册 creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored 事件，调用model对应whenXXXX函数
 * Class ModelObserver
 * @package App\Observers
 * @author klinson <klinson@163.com>
 */
class ModelObserver
{
    // 自动判断是否存在定义whenXXXX函数进行调用
    protected function _callEventMethod($model, $function)
    {
        $function_name = 'when'.ucfirst($function);

        if (method_exists($model, $function_name)) {
            $model->$function_name();
        }
    }

    // 自动注册 creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored 事件，只需重构对应whenXXXX实践
    public function creating($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function created($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function updating($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function updated($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function saving($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function saved($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function deleting($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function deleted($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function restoring($model) { $this->_callEventMethod($model, __FUNCTION__); }
    public function restored($model) { $this->_callEventMethod($model, __FUNCTION__); }
}