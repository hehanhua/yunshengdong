<?php


namespace app\wxapp\middleware;


use app\admin\model\AdminGroup;
use app\union\model\UnionsGroup;
use thans\jwt\facade\JWTAuth;

class JWTauthWxapp {

    //无需登录名单
    private $n = [
        'Index'          => true,
        'Login'          => true,
        'Account'        => ['index','set'],
        'Game'           => ['play','play2','info','appdown','wexin_openid','weiduan'],
        'Gift'           => ['index','gift_list_ajax','info'],
        'Shop'           => ['index','goods_detail'],
        'XcxTemp'     => ['getTemp'],
        'Wxapp'          =>true,
        'WxappTemp'          =>true,
        'Xcx'          =>true,
        'Api'          =>true,

    ];

    public function handle($request, \Closure $next) {
        //这个中间层校验有没有登录
        //如果没有登录就肯定时1001
        $controller = $request->controller();
        $action = $request->action();

        //不需要登录的控制器，直接过
        if(isset($this->n[$controller])){
            if($this->n[$controller] === true){
                return $next($request);
            }
            if(in_array($action,$this->n[$controller])){
                return $next($request);
            }
        }

        //$action = $request->action();
        if($controller == 'Login'){
            $request->union_id = 0;
            return $next($request);
        }else{
            try{
                $auth_info = JWTAuth::auth();
            }catch (\Exception $e){
                return json(['code'=>1001,'msg'=>$e->getMessage(),'data'=>[]]);
            }
        }

        $union_id = $auth_info['union_id']->getValue();
        $model_UnionsGroup = new UnionsGroup();
        $group_limits = $model_UnionsGroup -> getGroupLimitsByUnionId($union_id);
        $request->union_id = $union_id;
        //如果不存在这些控制器就不允许通过
        if(!in_array($controller,$group_limits)){
            return json(['code'=>0,'msg'=>'没有操作权限','data'=>[]]);
        }

        return $next($request);
    }
}