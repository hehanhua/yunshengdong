<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\wxapp;

use app\union\model\Unions;
use app\union\model\UnionsActionLog;
use thans\jwt\facade\JWTAuth;
use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = ['app\wxapp\middleware\JWTauthWxapp'];

    protected $user_id      = 0;
    protected $user         = [];
    protected $page         = 1;
    protected $limit        = 15;
    protected $cache_name   = '';


    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize() {
        $this->page = $this->request->param('page',1);
        $this->limit = $this->request->param('limit',15);

        try{
            $auth_info = JWTAuth::auth();
            $union_id = $auth_info['union_id']->getValue();

            $this->user_id = $union_id;
            $this->user = Unions::where('union_id',$union_id)->append(['group_name'])->findOrEmpty()->toArray();
        }catch (\Exception $e){
        }


        $this->cache_name = 'UNION_'.$this->request->controller().'_'.$this->request->action();
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }


    protected function suc($data,$msg=''){
        $this->addActionLog(1);
        return ['code'=>0,'msg'=>$msg,'data'=>$data];
    }
    protected function msuc($msg=''){
        $this->addActionLog(1);
        return ['code'=>0,'msg'=>$msg,'data'=>[]];
    }
    protected function merr($msg=''){
        $this->addActionLog(0);
        return ['code'=>1,'msg'=>$msg,'data'=>[]];
    }
    protected function psuc($data,$count,$msg=''){
        $this->addActionLog(1);
        if(empty($data)){
            $msg = '暂无数据';
        }
        return ['code'=>0,'count'=>$count,'msg'=>$msg,'data'=>$data];
    }
    protected function gsuc($paginate,$msg=''){
        $arr = $paginate->toArray();
        return $this->psuc($arr['data'],$arr['total'],$msg);
    }
    protected function sore($beal,$suc_msg='操作成功',$err_msg='操作失败'){
        return $beal ? $this->msuc($suc_msg) : $this->merr($err_msg);
    }
    private $log_out      = false;
    private $log_isdel    = false;
    private $log_name     = '(未设置)';
    private $log_describe = '(未设置)';
    protected function setActionLog($name,$describe,$isdel=false){
        $this->log_isdel = $isdel;
        $this->log_out = true;
        $this->log_name = $name;
        $this->log_describe = $describe;
    }

    private function addActionLog($status){
        if($this->log_out == false){
            return ;
        }
        //判断是不是删除
        if($this->log_isdel == true && $status == 1){
            $status = 2;
        }
        $data = [
            'union_id'  => $this->user_id,
            'ip'        => $this->request->ip(),
            'url'       => $this->request->url(),
            'status'    => $status,
            'name'      => $this->log_name,
            'describe'  => $this->log_describe,
        ];

        $model_UnionsActionLog = new UnionsActionLog();
        $model_UnionsActionLog -> save($data);
    }

}
