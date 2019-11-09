<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;

class Prize extends Model
{
    use SoftDeletes;
    const hasDefaultObserver = true;

    const redis_cache_quantity_key = 'prize_quantity_list';
    const THUMBNAIL_TEMPLATE = 'images/prize.png';

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }

    public function scopeLevelBy($query)
    {
        return $query->orderBy('level', 'desc');
    }

    /**
     * 抽奖算法,抽完库存减1
     * @param bool $is_test 是否测试
     * @return self|null
     * @author klinson <klinson@163.com>
     */
    public static function lottery($is_test = false)
    {
        $prizes = static::allByCache();
        $prize_rate = $prizes->sum('rate');
        $non_prize_rate = self::getNonPrizeRate();
        $all_rate = $prize_rate + $non_prize_rate;
        $result = null;
        foreach ($prizes as $prize) {
            $randNum = mt_rand(1, $all_rate);
            if ($randNum <= $prize->rate) {
                $result = $prize;
                break;
            } else {
                $all_rate -= $prize->rate;
            }
        }

        if ($result && !$is_test) {
            // 验证库存，成功减1则返回当前奖品，否则都是谢谢参与
            $surplus = Redis::hincrby(self::redis_cache_quantity_key, $result->id, -1);
            if ($surplus < 0) {
                Redis::hincrby(self::redis_cache_quantity_key, $result->id, 1);
                $result = null;
            }
        }

        return $result;
    }

    public static function getNonPrizeRate()
    {
        return config('system.non_prize_rate', 0);
    }

    public static function getNonPrizeRealRate()
    {
        $rate = self::getNonPrizeRate();
        return round(strval(floatval($rate) / (self::enabled()->sum('rate')+$rate) * 100), 2) . '%';
    }

    public static function allByCache($reset = false)
    {
        $key = 'enabled_prizes';
        if ($reset || app()->isLocal()) {
            cache()->delete($key);
        }

        return cache()->remember($key, 30, function () {
            return self::enabled()->levelBy()->ById()->get();
        });
    }

    public function initRedisQuantity()
    {
        Redis::hset(self::redis_cache_quantity_key, $this->id, $this->quantity);
    }

    // 重置缓存中的抽奖次数，慎重操作
    public static function resetRedisCacheCount()
    {
        self::chunk(100, function ($prizes) {
            Redis::pipeline(function ($pipe) use ($prizes) {
                foreach ($prizes  as $prize) {
                    $pipe->hset(self::redis_cache_quantity_key, $prize->id, $prize->quantity);
                }
            });
        });
    }

    public function getRealQuantityAttribute()
    {
        return intval(Redis::hget(self::redis_cache_quantity_key, $this->id));
    }

    public function whenCreated()
    {
        $this->initRedisQuantity();
    }

    public static function lotteryTest($number = 1000, $is_test = true)
    {
        $prizes = self::allByCache();

        foreach ($prizes as $prize) {
            $result[$prize->id] = [
                'id' => $prize->id,
                'title' => $prize->title,
                'rate' => $prize->rate,
                'count' => 0,
            ];
        }
        $result[0] = [
            'id' => 0,
            'title' => '谢谢参与',
            'rate' => self::getNonPrizeRate(),
            'count' => 0,
        ];

        while ($number--) {
            $prize = self::lottery($is_test);
            if (empty($prize)) {
                $prize_id = 0;
            } else {
                $prize_id = $prize->id;
            }
            $result[$prize_id]['count']++;
        }

        return $result;
    }

    public function getRealRateAttribute()
    {
        return round(strval(floatval($this->rate) / (self::enabled()->sum('rate')+self::getNonPrizeRate()) * 100), 2) . '%';
    }
}
