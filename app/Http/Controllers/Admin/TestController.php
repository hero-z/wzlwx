<?php
/**
 * Created by PhpStorm.
 * User: Hero
 * Date: 2018/1/18
 * Time: 0:44
 */

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

class TestController
{
    public function test(Request $request)
    {
        $url='http://huabei.qimengweixin.com/codetoany/getcode.php?auk=demo2';
        Header("Location: $url");
        dd($request->all());
    }
}