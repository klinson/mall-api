<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * 模型基类
 * 自动注册 creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored 事件，只需重构对应whenXXXX实践
 * Class Model
 * @package App\Models
 * @method whenCreating
 * @method whenCreated
 * @method whenUpdating
 * @method whenUpdated
 * @method whenSaving
 * @method whenSaved
 * @method whenDeleting
 * @method whenDeleted
 * @method whenRestoring
 * @method whenRestored
 * @author klinson <klinson@163.com>
 */
class Model extends EloquentModel
{
    protected $perPage = 10;
    const hasDefaultObserver = false;
    const cache_minutes = 360; //缓存6小时
    const cache_key = 'list:models'; //缓存列表数据key


    // 自动注册 creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored 事件，只需重构对应whenXXXX实践
    protected static function boot()
    {
        if (static::hasDefaultObserver) {
            static::observe(\App\Observers\ModelObserver::class);
        }
        parent::boot();
    }

    public function scopeIsMine($query)
    {
        return $query->where($this->getTable().'.user_id', \Auth::user()->id ?? 0);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeEnabled($query)
    {
        return $query->where('has_enabled', 1);
    }

    public function hasEnabled()
    {
        return $this->has_enabled === 1;
    }

    public function scopeById($query)
    {
        return $query->orderBy('id');
    }

    public function scopeSort($query)
    {
        return $query->orderBy('sort', 'desc');
    }

    public function getAdminLinkAttribute()
    {
        if (! $this->id) {
            return '';
        }
        $route_name = 'admin::'.lcfirst(\Illuminate\Support\Str::plural((class_basename(get_called_class())))).'.show';
        if (app('router')->has($route_name)) {
            return route($route_name, ['id' => $this]);
        } else {
            return '';
        }
    }

    public function getLinkAttribute()
    {
        if (! $this->id) {
            return '';
        }
        $route_name = lcfirst(\Illuminate\Support\Str::plural((class_basename(get_called_class())))).'.show';
        if (app('router')->has($route_name)) {
            return route($route_name, ['id' => $this]);
        } else {
            return '';
        }
    }

    /**
     * 生成后台form的模型select选择器
     * @param \Encore\Admin\Form $form
     * @param string $formField 存储表单字段
     * @param string $titles 选择下拉显示标题的字段，可以是title或者数组['id', 'title']或者id,title 多个会以|拼接
     * @param string $label 选择项目标题
     * @param boolean $is_all_options 是否一次获取全部
     * @param string $query_field 模糊查询字段
     * @param string $select_type 选择类型，可选参数select,multipleSelect
     * @author klinson <klinson@163.com>
     * @return $this|mixed
     */
    public static function form_display_select($form, $formField = '', $titles = 'title', $label = '', $is_all_options = true, $query_field = 'title', $select_type = 'select')
    {
        if (empty($formField)) {
            $formField = \Illuminate\Support\Str::snake(class_basename(get_called_class()), '_').'_id';
        }
        if (empty($label)) {
            $label = __(ucfirst(\Illuminate\Support\Str::snake(class_basename(get_called_class()), ' ') . ' id'));
        }
        if (! is_array($titles)) {
            $titles = explode(',', $titles);
        }

        if (count($titles) == 1) {
            if (! $is_all_options) {
                return $form->$select_type($formField, $label)->match(function ($keyword) use ($query_field, $titles) {
                    return static::where($query_field, 'LIKE', '%' . $keyword . '%')
                        // because select2 js plugin needs `text` and `id` column,
                        // so if your model does not contains these two, remember to AS for them
                        ->select([\DB::raw($titles[0].' AS text'), 'id'])
                        ->latest();
                })->text(function ($id) use ($titles) {
                    if (is_array($id)) {
                        return static::whereIn('id', $id)->select([\DB::raw($titles[0].' AS text'), 'id'])->pluck('text', 'id');
                    } else {
                        return static::where('id', $id)->select([\DB::raw($titles[0].' AS text'), 'id'])->pluck('text', 'id');
                    }
                    // return type is `{id1: text1, id2: text2...}
                });
            } else {
                return $form->$select_type($formField, $label)->options(static::all(['id', $titles[0]])->pluck($titles[0], 'id'));
            }
        } else {
            $selects = implode($titles, "`, ' | ', `");
            $selects = "concat(`{$selects}`) AS text";
            if (! $is_all_options) {
                return $form->$select_type($formField, $label)->match(function ($keyword) use ($query_field, $selects) {
                    return static::where($query_field, 'LIKE', '%' . $keyword . '%')
                        // because select2 js plugin needs `text` and `id` column,
                        // so if your model does not contains these two, remember to AS for them
                        ->select([\DB::raw($selects), 'id'])
                        ->latest();
                })->text(function ($id) use ($selects) {
                    if (is_array($id)) {
                        return static::whereIn('id', $id)->select([\DB::raw($selects), 'id'])->pluck('text', 'id');
                    } else {
                        return static::where('id', $id)->select([\DB::raw($selects), 'id'])->pluck('text', 'id');
                    }
                    // return type is `{id1: text1, id2: text2...}
                });
            } else {
                $list = static::all([\DB::raw($selects), 'id'])->pluck('text', 'id');
                return $form->$select_type($formField, $label)->options($list);
            }
        }
    }

    /**
     * 优先从缓存读数据
     * @param bool $reset 强行刷新
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author klinson <klinson@163.com>
     */
    public static function getByCache($reset = false)
    {
        if ($reset || app()->isLocal()) cache()->delete(static::cache_key);
        return cache()->remember(static::cache_key, static::cache_minutes, function () {
            $list = static::getAllData();
            $arr = [];
            foreach ($list as $item) {
                $arr[] = $item->formatToArray();
            }
            return $arr;
        });
    }

    // 获取所有数据列表
    public static function getAllData()
    {
        return static::all();
    }
    // 数据格式化
    public function formatToArray()
    {
        return $this->toArray();
    }
}
