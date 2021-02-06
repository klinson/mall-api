<?php
/**
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2019/11/21
 * Time: 9:34
 */

namespace App\Admin\Controllers\Traits;


/**
 * 是否有缩略图
 * Trait HasThumbnail
 * @package App\Admin\Controllers\Traits
 */
trait HasThumbnail
{
    // 缩略图默认图
    protected $thumbnail_template = 'images/template.jpg';

    // 通用缩略图
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset($this->thumbnail_template);
        }
    }
}
