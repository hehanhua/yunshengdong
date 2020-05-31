<?php


namespace app\union\validate;


use app\union\model\Unions;
use think\Validate;

class Login extends Validate {

    protected $rule = [
        'username'  => 'require',
        'password'  => 'require|checkPass'
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'password.require' => '密码不能为空',
    ];

    protected  $scene = [
        'login' => ['username','password']
    ];

    //校验密码是否正确
    protected function checkPass($value,$rule,$data){
        $admin_password = Unions::where('union_name',$data['username'])->value('union_password');
        if(sha1($value) == $admin_password){
            return true;
        }else{
            return '密码不正确';
        }
    }

}