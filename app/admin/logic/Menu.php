<?php


namespace app\admin\logic;

//该类负责后台的菜单操作
use app\admin\model\AdminGroup;

class Menu {

    private $my_menu = [];
    private $group_limit = [];
    private $group_id = 0;

    //生成一个list的方法
    private function makeList($data){
        $arr = [];
        foreach ($data as $value){
            if($this->group_id == 1){
                $arr[] = [
                    'name'=>$value[0],
                    'jump'=>$value[1],
                    'title'=>$value[2]
                ];
            }else{
                if(in_array($value[0],$this->group_limit)){
                    $arr[] = [
                        'name'=>$value[0],
                        'jump'=>$value[1],
                        'title'=>$value[2]
                    ];
                }
            }
        }
        return $arr;
    }

    //生成一个group的方法
    private function makeGroup($head,$data){
        //先创建标题
        $list = $this->makeList($data);
        if(empty($list)){
            return ;
        }
        $arr = [
            'title'=>$head[0],
            'icon'=>$head[1],
            'list'=>$list
        ];
        array_push($this->my_menu,$arr);
    }

    public function getMenu($group_id = 0){
        //读取这个分组的权限
        $this->group_id = $group_id;

        if($group_id != 0){
            $model_AdminGroup = new AdminGroup();
            $this->group_limit = $model_AdminGroup->getGroupLimitsById($group_id);
        }
        $this->makeMenu();
        return $this->my_menu;
    }

    private function makeMenu(){
        $this->makeGroup(['红人','layui-icon-form'],[
            ['HotPerson','hot_person/hot_person/index','红人'],
        ]);

        $this->makeGroup(['视频','layui-icon-form'],[
            ['HotPerson','hot_video/hot_video/index','视频'],
        ]);

        $this->makeGroup(['系统','layui-icon-set-fill'],[
            ['SystemPass','set/user/password','修改密码'],
            ['Admin','admin/admin/admin','管理员'],
            ['AdminGroup','admin/group/group','管理员权限组'],
            ['SystemCache','system/cache/cache','刷新缓存'],
        ]);
    }

}