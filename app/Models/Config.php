<?php
/**
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2019/10/29
 * Time: 16:30
 */

namespace App\Models;

use App\Handlers\ConfigHandler;
use Encore\Admin\Config\ConfigModel;

class Config extends ConfigModel
{
    protected $fillable = ['value', 'name'];

    protected static function boot()
    {
        static::saved(function () {
            ConfigHandler::getConfigs(true);
        });
        parent::boot();
    }
}