<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/21
 * Time: 17:10
 */

namespace App\Api\Controllers;


use App\Commen\WxLog;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VerifyController extends WxBaseController
{
    public function Verify(Request $request)
    {
        $code=0;
        $msg='success';
        WxLog::write($request->all(),'test.txt');
        $data=$request->all();
        $ck=true;
        if(!is_array($data)){
            $code=1000;
            $msg='数据请求格式错误!';
            $ck=false;
        }
        if(!array_key_exists('raw_data',$data)){
            $code=1001;
            $msg='参数错误,不存在raw_data';
            $ck=false;
        }
        if(!array_key_exists('app_key',$data)){
            $code=1002;
            $msg='参数错误,不存在app_key';
            $ck=false;
        }
        if($ck){
            try{
                $rawdata=$data['raw_data'];
                Log::info($rawdata);
                $data=self::decrypt($rawdata,$this->app_key,$this->app_secret);
                $resdata=json_decode($data,true);
                Log::info($resdata);
                if(is_array($resdata)&&array_key_exists('sign',$resdata)
                    &&array_key_exists('card_number',$resdata)
                    &&array_key_exists('password',$resdata))
                {
                    if(self::checkSign(array_except($resdata,['sign']),$this->app_secret,$resdata['sign'])){
//            array (
//                'card_number' => '111111',
//                'password' => 'helloworld',
//                'app_key' => 'O4vcFIEd29GLbNpk',
//                'nonce_str' => 'VXh8ZDl0FurcRzmXFaudnC1RiASOtRqo',
//                'timestamp' => 1516536009,
//                'sign' => 'F0DB5F5AE555046B374E061424EC67F9',
//            )
                        $card_no=$resdata['card_number'];
                        $password=$resdata['password'];
                        $student=Student::where('code_number',$card_no)->first();
                        if($student){
                            //校验登录信息
                            if(Hash::check($password,$student->password)){
                                if(!$student->pay_lock){
//                                    {
//                                        "card_number":"07302590", //校园账号，一般是学号，必填
//  "name":"张三丰", //学生姓名，必填
//  "gender": "男", // 性别：男/女
//  "head_image": "http://xxx/xx.png"  // 学生头像地址
//  "grade":"2016", //年级，学生必填
//  "college":"信息科学与技术学院", //学院，学生必填
//  "profession":"计算机系", // 专业，学生必填
//  "class":"软件1班", // 班级
//  "identity_type":1, // 身份类型：0-其他；1-学生；4-教职工；5-校友
//  "identity_title": "学生", // 职称: "讲师"、“研究生”、“教授”
//  "id_card":"4XXX***7", // 身份证号码
//  "telephone":"137***8" // 手机号
//}
                                    $return_raw_data=[
                                        'card_number'=>$student->code_number,
                                        'name'=>$student->name,
                                        'gender'=>$student->sex==1?'男':'女',
                                        'head_image'=>$student->head_image,
                                        'grade'=>$student->session,
                                        'college'=>'ces',
                                        'profession'=>'ces11',
                                        'class'=>'ces11',
                                        'identity_type'=>1,
                                        'identity_title'=>'学生',
                                        'id_card'=>$student->id_code,
                                        'telephone'=>$student->phone,
                                    ];
                                    Log::info($return_raw_data);
                                    $encryptdata=self::encrypt(json_encode($return_raw_data),$this->app_key,$this->app_secret);
                                    return  json_encode([
                                        "code"=>$code,
                                        "message"=>$msg,
                                        "raw_data"=>$encryptdata,
                                        "app_key"=>$resdata['app_key'],
                                    ]);
                                }else{
                                    $code=1007;
                                    $msg='学生账号已经被锁定!';
                                }
                            }else{
                                $code=1006;
                                $msg='学生账号或者密码不正确!';
                            }
                        }else{
                            $code=1005;
                            $msg='学生账号信息不存在!';
                        }
                    }else{
                        $code=1004;
                        $msg='验签失败!';
                    }
                }else{
                    $code=1003;
                    $msg='数据有误!';
                }
            }catch (\Exception $e){
                $code=$e->getCode();
                $msg=$e->getMessage();
                Log::Info($e);
                WxLog::write($e,'error.txt');
            }
        }
        return [
            "code"=>$code,
            "message"=>$msg,
        ];
    }

}