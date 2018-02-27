<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/21
 * Time: 3:01
 */

namespace App\Api\Controllers;


use App\Commen\WxLog;
use Illuminate\Support\Facades\Log;

class TestController extends WxBaseController
{
    public function test()
    {
        return 'success';
    }
    public function getData()
    {
        try{
            $data=[
                "card_number"=>"111111",
                "password"=>"helloworld",
                "app_key"=>$this->app_key,
                "nonce_str"=>self::getNonce(),
                "timestamp"=>time()
            ];
            $sign=self::sign($data,$this->app_secret);
            $data['sign']=$sign;
            $encryptData=self::encrypt(json_encode($data),$this->app_key,$this->app_secret);
            return json_encode([
                "code"=>0,
                "message"=>"success",
                "raw_data"=>$encryptData,
                "app_key"=>$this->app_key
            ]);
        }catch (\Exception $e){
//            WxLog::write($e);
            Log::info($e);
        }
    }
}