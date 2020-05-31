<?php


namespace app\admin\controller;


use app\admin\BaseController;
use app\admin\logic\Menu;
use app\admin\model\H5GameCommon;
use app\admin\model\H5GameCompany;
use app\admin\model\H5GameCategory;
use app\admin\model\H5NewsClass;
use app\admin\model\IssueH5Company;
use app\admin\model\IssueH5Sdk;
use app\admin\model\Unions;
use think\facade\App;
use think\facade\Db;

class Common extends BaseController {

    //公司名称
    public function company_list(){
        $compay_list = H5GameCompany::select();
        return json($this->suc($compay_list));
    }

    //渠道公司名称
    public function issue_h5_company_list(){
        $compay_list = IssueH5Company::select();
        return json($this->suc($compay_list));
    }

    //小程序模板
    public function wxapp_temp_list(){
        $list = \app\admin\model\WxappTemp::select();
        return json($this->suc($list));
    }

    //游戏type
    public function category_list(){
        $type_list = H5GameCategory::select();
        return json($this->suc($type_list));
    }

    //h5游戏列表
    public function h5game_list(){
        $game_list = H5GameCommon::field(['game_id','game_name'])->order('game_id','DESC')->select();
        return json($this->suc($game_list));
    }

    //资讯列表
    public function h5newsclass_list(){
        $class_list = H5NewsClass::select();
        return json($this->suc($class_list));
    }

    //获取unions信息
    public function unions_list(){
        $unions_list = Unions::field(['union_id','union_name'])->order('union_id','DESC')->select();
        return json($this->suc($unions_list));
    }
    //获取CPS信息
    public function cps_list(){
        $unions_list = Unions::field(['union_id','union_name'])->order('union_id','DESC')->where('union_role','cps')->select();
        return json($this->suc($unions_list));
    }

    //获取渠道信息
    public function channel_list(){
        $unions_list = Unions::field(['union_id','union_name'])->order('union_id','DESC')->where('union_role','channel')->select();
        return json($this->suc($unions_list));
    }


    //获取所有分发渠道
    public function issue_sdk_list(){
        $sdk_list = IssueH5Sdk::field(['sdk_id','sdk_name','sdk_alias'])->order('sdk_id','DESC')->select();
        return json($this->suc($sdk_list));
    }

    //获取所有支付方式
    public function payment_list(){
        return json($this->suc([
            'alipay'            ,
            'alipay_app'        ,
            'alipaykf'          ,
            'wechat'            ,
            'wechat_miniapp'    ,
            'wechat_native'     ,
            'wechat_app'        ,
            'wechat_jsapi'      ,
            'wechat_mweb'       ,
            'wechatkf'          ,
            'coin'              ,
            'issue'             ,
            'admin'             ,
            'activity'          ,
        ]));
    }

    //系统信息
    public function sysinfo(){
        return json($this->suc([
            'tpv'       => 'ThinkPHP '.App::version(),
            'systime'   => date("Y-n-j H:i:s"),
            'server_software'   => $this->request->server('SERVER_SOFTWARE'),
            'php'   =>'PHP '.phpversion(),
            'db'    => 'Mysql '.Db::query('select version() as ver')[0]['ver'],
            'php_uname'=> php_uname(),
            'php_sapi_name' => php_sapi_name(),
            'upload_max_filesize'   => ini_get('upload_max_filesize'),
            'post_max_size'   => ini_get('post_max_size'),
        ]));
    }


}