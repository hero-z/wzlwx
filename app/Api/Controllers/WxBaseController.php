<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/21
 * Time: 18:31
 */

namespace App\Api\Controllers;


use Illuminate\Support\Facades\Log;

class WxBaseController
{
    public $app_key="A63DED04E4CB8030";
    public $app_secret="DA215937BF3DF680CB773F7C27D39969";
    public static function sign($param_array,$app_secret=null){
        $names = array_keys($param_array);
        sort($names, SORT_STRING);
        $item_array = array();
        foreach ($names as $name){
            $item_array[] = "{$name}={$param_array[$name]}";
        }
        $str = implode('&', $item_array) . '&key=' . $app_secret;
        return strtoupper(md5($str));
    }
    public static function checkSign($param_array,$app_secret,$sign){
        $names = array_keys($param_array);
        sort($names, SORT_STRING);
        $item_array = array();
        foreach ($names as $name){
            $item_array[] = "{$name}={$param_array[$name]}";
        }
        $str = implode('&', $item_array) . '&key=' . $app_secret;
        Log::info($str);
        Log::info(strtoupper(md5($str)));
        Log::info(strtoupper($sign));
        return strtoupper(md5($str))==strtoupper($sign);
    }
    public static function encrypt($str, $key, $iv)
    {
        $str = $str . str_repeat("\0", 16 - strlen($str) % 16);
        //截取长度$iv
        $iv=substr($iv, 0,16);
        $encrypt = openssl_encrypt($str, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

        return bin2hex($encrypt);
    }

    public static function decrypt($str, $key, $iv)
    {
        //截取长度$iv
        $iv=substr($iv, 0,16);
        $decrypt = openssl_decrypt(hex2bin($str), 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        return rtrim($decrypt, "\0");
    }
    public static function getNonce($num=32,$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        $str='';
        $length = strlen($keyspace)-1;
        for ($i=0;$i<$num;$i++){
            $start=rand(0,$length);
            $str.=substr($keyspace, $start,1);
        }
        return $str;
    }
}