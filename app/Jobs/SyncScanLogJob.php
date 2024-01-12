<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\ScanLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SyncScanLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Device $device)
    {
        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $session = true;
            $url = $this->device->server_ip.":". $this->device->server_port."/scanlog/all/paging";
            while($session){
                $result = Http::asForm()->post($url, [
                    'sn' =>  $this->device->sn
                ])->json();
                if(isset($result['Result'])){
                    if($result['Result']){
                        foreach ($result['Data'] as $res) {
                            $data[] = array(
                                'sn' =>$res['SN'],
                                'scan_date' => $res['ScanDate'],
                                'pin' =>$res['PIN'],
                                'verify_mode' => $res['VerifyMode'],
                                'work_code' => $res['WorkCode'],
                                'io_mode' => $res['IOMode'],
                            );
                        }
                        ScanLog::insertOrIgnore($data);
                    }
                    $session = $result['IsSession'] ?? false;
                }else{
                    $session = false;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
           
    }
}
