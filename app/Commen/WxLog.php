<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/21
 * Time: 17:24
 */

namespace App\Commen;


class WxLog
{
    public static $max_file_size=1*1024*1024;//不大于1M

    /*
        传入log文件绝对路径
    */
    public static function write($data,$file='')
    {
        if(empty($file))
        {
            $file=storage_path().'/logs/weixiao/log.txt';
        }
        else
        {
            $file=storage_path().'/logs/weixiao/'.$file;
        }
        $check=self::checkFile($file);
        if($check)
        {
            self::renameFile($file);
        }

        $str = "\r\n";
        $str .= "\r\n";
        $str .='============='.date('Y-m-d H:i:s').'======================'."\r\n";
        $str.=var_export($data,true);

        file_put_contents($file, $str,FILE_APPEND);
    }


    public static function renameFile($file)
    {

        $info=pathinfo($file);//array ( 'dirname' => './s', 'basename' => 'chencai.log.txt', 'extension' => 'txt', 'filename' => 'chencai.log', )

        $newfile=$info['dirname'].'/'.$info['filename'].'_'.date('YmdHis').'_'.mt_rand(10,99).'.'.$info['extension'];
        rename($file, $newfile);

    }

    // 如果超过规定日志大小，返回true；否则返回false
    public static function checkFile($file)
    {
        clearstatcache();
        if(file_exists($file)&&(filesize($file)>self::$max_file_size))
        {
            return true;
        }
        return false;
    }

}