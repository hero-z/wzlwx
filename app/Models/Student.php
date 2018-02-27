<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable=[
        'code_number', 'id_type', 'id_code', 'name', 'sex', 'nation', 'session', 'msg', 'password', 'pay_key', 'pay_lock','organization','head_image','phone', 'created_at', 'updated_at'
    ];
}
