<?php

namespace App\Console\Commands;

use App\Jobs\SyncScanLogJob;
use App\Models\Device;
use Illuminate\Console\Command;

class SyncScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:sync-scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('-- Run Sync Scan Log --');
        $devices = Device::all();
        foreach ($devices as $device) {
            # code...
            $this->info('Start '.$device->name);
            SyncScanLogJob::dispatch($device);
            $this->info('End '.$device->name);
            $this->info('-----');
        }
    }
}
