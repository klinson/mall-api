<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户批处理';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->init();
            }
        });
    }
}
