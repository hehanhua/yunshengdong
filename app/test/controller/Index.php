<?php
namespace app\test\controller;
use app\pc\BaseController;
use think\facade\View;

class Index extends BaseController {

   public function index(){
        $mima = 'hehanhua1';
       var_dump(password_encode($mima));
   }

}
