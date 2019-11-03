<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use App\Transformers\PrizeTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryRecord extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'user_id', 'prize_id', 'prize_snapshot', 'chance_id', 'status'
    ];

    protected $casts = [
        'prize_snapshot' => 'array'
    ];

    public static function generateRecord(User $user, Prize $prize, LotteryChance $chance)
    {
        $prize->decrement('quantity', 1);
        $prize->save();

        $record = new self([
            'user_id' => $user->id,
            'prize_id' => $prize->id,
            'prize_snapshot' => (new PrizeTransformer())->transform($prize),
            'chance_id' => $chance->id,
            'status' => 1,
        ]);
        $record->save();

        return $record;
    }
}
