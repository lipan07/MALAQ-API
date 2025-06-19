<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class SetUsersOffline extends Command
{
    protected $signature = 'users:set-offline';
    protected $description = 'Set users status to offline if last_activity is more than 5 minutes ago';

    public function handle()
    {
        $threshold = Carbon::now('UTC')->subMinutes(5);

        $count = User::where('status', 'online')
            ->where('last_activity', '<', $threshold)
            ->update(['status' => 'offline']);

        $this->info("Set $count users to offline.");
    }
}
