<?php


namespace app\admin\model;


use think\Model;

class Admin extends Model {

    protected $pk = 'admin_id';
    protected $autoWriteTimestamp = true;
    protected $json = ['super_permission'];
    protected $schema = [
        'admin_id' => 'int',
        'admin_name' => 'string',
        'admin_password' => 'string',
        'reg_time' => 'int',
        'ip' => 'string',
        'last_time' => 'int',
    ];

    public function getLoginTimeAttr($value){
        return format_intdate($value);
    }

    //设置密码
    public function setAdminPasswordAttr($value){
        return password_encode($value);
    }

    public function setReginTimeAttr($value){
        return time();
    }

    //添加一个管理员
    public function addAdmin($data){
        return $this->save($data);
    }


}