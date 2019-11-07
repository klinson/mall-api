<?php

namespace App\Jobs;

use App\Models\LotteryRecord;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutoReceiveLotteryRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record_id;

    /**
     * 定时去自动确认签收
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record_id)
    {
        $this->record_id = $record_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $record = LotteryRecord::find($this->record_id);
        if ($record && $record->status === 2) {
            $record->receive();
        }
    }

    public function tags()
    {
        return ['AutoReceiveLotteryRecordJob', 'Record:'.$this->record_id];
    }
}
