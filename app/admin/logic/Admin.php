<?php


namespace app\admin\logic;


use app\admin\validate\Login;
use think\exception\ValidateException;
use think\facade\Request;
use think\Model;

class Admin extends Model {


    //管理员登录之后的操作
    public function loginAfter($username){
        //更新记录，ip和login_time
        $model_Admin = new \app\admin\model\Admin();
        $model_Admin->where('admin_name',$username)->save([
            'ip'    => Request::ip(),
            'login_time' => time()
        ]);
        //获取token
    }

    //修改密码
    public function editPassWord($admin_id,$oldpass,$newpass){
        //取出这个用户的密码
        $pass = \app\admin\model\Admin::where('admin_id',$admin_id)->value('admin_password');
        if(password_encode($oldpass) == $pass){
            //修改密码
            $res = \app\admin\model\Admin::where('admin_id',$admin_id)->save(['admin_password'=>password_encode($newpass)]);
            return $res ? true:false;
        }else{
            return false;
        }
    }

}