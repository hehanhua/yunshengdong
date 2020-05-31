<?php
namespace app\admin\controller;
use app\admin\BaseController;
use app\admin\model\Admin;
use thans\jwt\facade\JWTAuth;
use think\exception\ValidateException;
use think\facade\View;

class Index extends BaseController {

    public function index(){

        return View::fetch();
    }
    public function login(){

        $username = $this->request->param('username');
        $password = $this->request->param('password');

        //验证密码是否正确
        try{
            validate(\app\admin\validate\Login::class)->scene('login')->check([
                'username' => $username,
                'password' => $password
            ]);

        }catch (ValidateException $e){
            //登录失败
            return json($this->merr($e->getError()));
        }
        //这里其实已经登录成功了
        $model_Admin = new Admin();
        $admin_info = $model_Admin -> where('admin_name',$username)->findOrEmpty();
        if($admin_info->isEmpty()){
            return json($this->merr('不存在该用户'));
        }
        //更新ip和最后登录时间
        $model_Admin->where('admin_id',$admin_info['admin_id'])->save([
            'ip'    => $this->request->ip(),
            'last_time' => time()
        ]);
        //编写行为日志，就这里比较特殊，需要赋值到父级
        $this->user_id = $admin_info['admin_id'];
        $this->user = $admin_info;
        //获取token
        $token = JWTAuth::builder(['admin_id'=>$admin_info['admin_id']]);
        return json($this->suc(['access_token'=>$token]));
    }

    public function logout(){
        JWTAuth::refresh();
        return json($this->msuc('退出成功'));
    }

}
