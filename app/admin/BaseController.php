<?php
declare (strict_types = 1);

namespace app\admin;

use app\admin\model\Admin;
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
    protected $middleware = ['app\admin\middleware\JWTauthAdmin'];

    protected $user_id = 0;
    protected $page         = 1;
    protected $limit        = 15;
    protected $user = [];

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
    protected function initialize()
    {
        try{
            $auth_info = JWTAuth::auth();
            $admin_id = $auth_info['admin_id']->getValue();
            $this->user_id = $admin_id;
            $this->user = Admin::where('admin_id',$admin_id)->append(['group_name'])->findOrEmpty()->toArray();
        }catch (\Exception $e){

        }
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
                [$validate, $scene] = explode('.', $validate);
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
//        $this->addActionLog(1);
        return ['code'=>0,'msg'=>$msg,'data'=>$data];
    }
    protected function msuc($msg=''){
//        $this->addActionLog(1);
        return ['code'=>0,'msg'=>$msg,'data'=>[]];
    }
    protected function merr($msg=''){
//        $this->addActionLog(0);
        return ['code'=>1,'msg'=>$msg,'data'=>[]];
    }
    protected function psuc($data,$count,$msg=''){
//        $this->addActionLog(1);
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
}
