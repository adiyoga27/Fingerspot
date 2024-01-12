<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FingerspotController extends Controller
{
    function getDevice() {
        $devices = Device::all();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $devices
        ]);
    }

    function getScanLogAll(Request $request) {
        $devices = Device::all();

        foreach ($devices as $device) {
            $url = $device->server_ip.":".$device->server_port."/scanlog/all/paging";
           
            $data[] =$this->checkFingerspot($url, $device->sn);
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }

    // function getScanLogBySN(Request $request, $keyword) {
    //     $devices = Device::all();

    //     foreach ($devices as $device) {
    //         $url = "http://".$device->server_ip.":".$device->server_port."/scanlog/all/paging";
    //         $result = Http::post($device->server_ip, [
    //             'sn' => $device->sn
    //         ])->withHeader([
    //             'Content-Type' => 'x-www-form-urlencoded'
    //         ]);
    //         $data[] = $result->json();
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'success',
    //         'data' => $data
    //     ]);
        
    // }

    function checkFingerspot($url, $sn) {

        $session = true;
        $data = [];
        while($session){
            $result = Http::asForm()->post($url, [
                'sn' => $sn
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
                $session = $result['IsSession'];
            }else{
                $session = false;
            }
        }
        return $data;
    }
}
