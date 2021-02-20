<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use ModelTree, AdminBuilder, SoftDeletes;
    const hasDefaultObserver = true;
    const cache_key = 'list:categories';

    protected $appends = ['thumbnail_url'];
    protected $hidden = ['deleted_at'];
    protected $casts = ['search_ids' => 'array'];

    protected $fillable = ['title', 'code', 'parent_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->orderColumn = 'sort';
    }

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function whenCreating()
    {
        $this->search_ids = [];
    }

    public function whenSaving()
    {
        $this->full_title = $this->getNewFullTitle();
    }

    public function whenSaved()
    {
        $this->updateSearchIds();
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }

    public function getAdCodeAttribute()
    {
        return 'category-'.$this->id;
    }

    public function getNewFullTitle()
    {
        $full_title = '';
        if ($this->parent) {
            $full_title .= $this->parent->full_title . '/';
        }
        return $full_title.$this->title;
    }

    // 更新搜索ID列表，递归父级
    public function updateSearchIds()
    {
        $res = $this->getAllChildren();
        if ($res->count() > 0) $child_ids = $res->pluck('id')->toArray();
        else $child_ids = [];
        $child_ids[] = $this->id;
        if (!$this->search_ids || array_diff($child_ids, $this->search_ids)) {
            $this->search_ids = $child_ids;
            $this->save();
        }
//        if ($this->parent) {
//            $this->parent->updateSearchIds();
//        }
    }

    // 获取所有子对象
    public function getAllChildren()
    {
        if ($this->children) {
            $children = $this->children;
            foreach ($this->children as $child) {
                $cc = $child->getAllChildren();
                if ($cc->count() > 0) $children = $children->merge($cc);

            }
            return $children;
        } else {
            return collect();
        }
    }

    public static function getTreeList()
    {
        $list = static::enabled()->orderBy('sort')->get()->toArray();

        $tree = list_to_tree($list, 0, 'id', 'parent_id');
        return $tree;
    }

    public static function getByCache($reset = false)
    {
        if ($reset || app()->isLocal()) cache()->delete(static::cache_key);
        return cache()->remember(static::cache_key, static::cache_minutes, function () {
            $list = static::getTreeList();

            return $list;
        });
    }
}
