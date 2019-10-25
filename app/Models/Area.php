<?php

namespace App\Models;

class Area extends Model
{
    public $timestamps = false;

    public function formatFullName()
    {
        // 全国或省或直辖市
        if ($this->parent_id === 0 || $this->parent_id === 1) {
            $this->full_name = $this->name;
            $this->type = $this->parent_id;
            $this->save();
            return ;
        }
        // 市
        if ($this->parent->parent_id === 1) {
            $this->type = 2;
            $this->full_name = $this->parent->name . '/' . $this->name;
            $this->save();
            return ;
        }
        // 区
        if ($this->parent->parent->parent_id === 1) {
            $this->type = 3;
            $this->full_name = $this->parent->parent->name . '/' . $this->parent->name . '/' . $this->name;
            $this->save();
            return ;
        }
        // 镇
        if ($this->parent->parent->parent->parent_id === 1) {
            $this->type = 4;
            $this->full_name = $this->parent->parent->parent->name . '/' . $this->parent->parent->name . '/' . $this->parent->name . '/' . $this->name;
            $this->save();
            return ;
        }
    }

    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id', 'id');
    }
}
