<?php

namespace app\admin\middleware;

use thans\jwt\facade\JWTAuth;
use think\exception\ErrorException;

class JWTauthAdmin {
    public function handle($request, \Closure $next) {
        //这个中间层校验有没有登录
        //如果没有登录就肯定时1001
        $controller = $request->controller();
        $action = $request->action();

        if($controller == 'Index'&&$action == 'index'|| $controller == 'Index'&&$action == 'login'){
            $request->admin_id = 0;
            return $next($request);
        }else{
            try{
                $auth_info = JWTAuth::auth();
            }catch (\Exception $e){
                return json(['code'=>1003,'msg'=>$e->getMessage(),'data'=>[]]);
            }
        }
        try{
            $admin_id = $auth_info['admin_id']->getValue();
        }catch (ErrorException $e){
            return json(['code'=>1002,'msg'=>$e->getMessage(),'data'=>[]]);
        }

        return $next($request);



    }
}
