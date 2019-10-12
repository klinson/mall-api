<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-6-15
 * Time: 上午9:39
 */

namespace App\Models\Traits;

/**
 * 模型拥有所有者属性
 * Trait HasOwnerHelper
 * @package App\Models\Traits
 */
trait HasOwnerHelper
{
    protected $ownerModel = \App\Models\User::class;
    protected $ownerForeignKey = 'user_id';
    protected $ownerModelKey = 'id';

    public function owner()
    {
        return $this->belongsTo($this->ownerModel, $this->ownerForeignKey, $this->ownerModelKey);
    }

    public function scopeIsOwner($query)
    {
        return $query->where($this->getTable().'.'.$this->ownerForeignKey, \Auth::user()->id ?? 0);
    }
}