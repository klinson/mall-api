<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class CofferWithdrawal extends Model
{
    use HasOwnerHelper, SoftDeletes;

    protected $fillable = ['balance', 'status', 'ip', 'checked_at'];

    // 1-申请中，2申请通过，3驳回
    const status_text = [
        1 => '审核中',
        2 => '申请通过',
        3 => '驳回',
    ];

    // 拒绝
    public function reject()
    {
        if ($this->status !== 1) return false;
        try {
            $this->fill([
                'status' => 3,
                'checked_at' => date('Y-m-d H:i:s')
            ]);
            $this->save();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    // 审核通过
    public function pass()
    {
        if ($this->status !== 1) return false;
        try {
            DB::beginTransaction();

            $this->owner->coffer->withdrawal($this);

            $this->fill([
                'status' => 2,
                'checked_at' => date('Y-m-d H:i:s')
            ]);
            $this->save();

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
