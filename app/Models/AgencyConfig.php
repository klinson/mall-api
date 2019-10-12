<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyConfig extends Model
{
    use SoftDeletes;

    public function orders()
    {
        return $this->hasMany(RechargeThresholdOrder::class, 'agency_config_id', 'id');
    }

}
