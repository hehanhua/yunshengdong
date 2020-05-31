<?php


namespace app\admin\controller;


use app\admin\BaseController;
use app\admin\logic\Menu;
use think\exception\ValidateException;

class User extends BaseController {

    public function info(){
        $user_info = $this->user;
        unset($user_info['admin_password']);
        return json($this->suc($user_info));
    }

    public function menu(){
        $logic_Menu = new Menu();
        $menu = $logic_Menu -> getMenu($this->user['group_id']);
        if($menu === false){
            return json($this->msuc('获取菜单失败'));
        }else{
            return json($this->suc($menu));
        }
    }

    public function menu_all(){
        $logic_Menu = new Menu();
        $menu = $logic_Menu -> getMenu(0);
        if($menu === false){
            return json($this->msuc('获取菜单失败'));
        }else{
            return json($this->suc($menu));
        }
    }

    public function editpass(){
        $oldPassword = $this->request->post('oldpassword');
        $password = $this->request->post('password');


        try{
            validate(\app\admin\validate\Password::class)->scene('edit')->check($this->request->post());
        }catch (ValidateException $e){
            //登录失败
            return json($this->merr($e->getError()));
        }

        if(password_encode($oldPassword) != $this->user['admin_password']){
            return json($this->merr('旧密码错误'));
        }
        $logic_Admin = new \app\admin\logic\Admin();
        $res = $logic_Admin -> editPassWord($this->user_id,$oldPassword,$password);
        if($res){
            return json($this->msuc(''));
        }else{
            return json($this->merr(''));
        }
    }

}