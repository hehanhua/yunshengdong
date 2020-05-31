<?php
namespace app\pc\controller;
use app\pc\BaseController;
use think\facade\View;

class Index extends BaseController {

    public function index(){

        return View::fetch();

    }

    public function address(){

        return View::fetch();

    }

    public function video(){

        return View::fetch();

    }

}
