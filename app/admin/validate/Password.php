<?php


namespace app\admin\validate;


use think\Validate;

class Password extends Validate {
    protected $rule = [
        'oldpassword'  => 'require',
        'password'  => 'require',
        'repassword'  => 'require|checkre'
    ];

    protected $message = [
        'oldpassword.require' => '旧密码不能为空',
        'password.require' => '新密码不能为空',
        'repassword.require' => '重复密码不能为空',
    ];

    protected  $scene = [
        'edit' => ['oldpassword','password','repassword']
    ];

    protected function checkre($value,$rule,$data){
        if($data['password'] === $data['repassword']){
            return true;
        }else{
            return '两次密码不一致';
        }
    }

}