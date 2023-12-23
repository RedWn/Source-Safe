<?php

namespace App\Console\Commands;

use App\Models\Checkin;
use App\Models\File;
use Illuminate\Console\Command;

class HandleOverdueCheckinsCommand extends Command
{
    protected $signature = 'handle-overdue-checkins';

    protected $description = 'Command description';

    public function handle(): void
    {
        $fileIdsToUpdate = [];
        $checkinIdsToUpdate = [];

        $overdueCheckins = Checkin::where('done', false)
            ->whereDate('checkout_date', '>=', now())
            ->get();

        foreach ($overdueCheckins as $checkin) {
            $checkinIdsToUpdate[] = $checkin->id;
            $fileIdsToUpdate[] = $checkin->fileID;
        }

        Checkin::whereIn('id', $checkinIdsToUpdate)->update(['done' => true]);

        if (count($fileIdsToUpdate) == 0)
            echo "No overdue checkins were found.\n";
        else
            echo count($fileIdsToUpdate) . " overdue checkins were updated.\n";
    }
}
