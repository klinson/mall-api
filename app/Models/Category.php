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

    protected $fillable = ['title', 'code', 'parent_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->orderColumn = 'sort';
    }

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function whenSaving()
    {
        $this->full_title = $this->getNewFullTitle();
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
