<?php


namespace app\admin\model;


use think\facade\Cache;
use think\Model;

class AdminGroup extends Model {

    protected $pk = 'group_id';

    protected $json = ['group_limits'];

    protected $schema = [
        'group_id'          => 'int',
        'group_name'        => 'string',
        'group_limits'      => 'string'
    ];

    public function getGroupLimitsById($gourp_id){
        $cache_name = 'group_list_byid_'.$gourp_id;
        if(Cache::has($cache_name)){
            return Cache::get($cache_name);
        }else{
            $group_limits = $this->where('group_id',$gourp_id)->value('group_limits');
            //这里应该加缓存的。
            $group_limits = json_decode($group_limits,true);
            if(is_array($group_limits)){
                array_push($group_limits,'User');
                array_push($group_limits,'Common');
                Cache::tag(['admin','admin_group'])->set($cache_name,$group_limits);
                return $group_limits;
            }else{
                return [];
            }
        }
    }


    //通过admin_id获取权限组列表
    public function getGroupLimitsByAdminId($admin_id){
        $group_id = Admin::where('admin_id',$admin_id)->value('group_id');
        return $this->getGroupLimitsById($group_id);
    }
}