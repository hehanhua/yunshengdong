<?php


namespace app\admin\controller;


use app\admin\BaseController;
use think\facade\Filesystem;


class HotPerson extends BaseController {

    public function person_list(){
        $modelPerson = new \app\admin\model\HotPerson();
        $list = $modelPerson->select();
        return json($this->suc($list));
    }

   public function addPerson(){
       $modelPerson = new \app\admin\model\HotPerson();
       $data = $this->request->param();
       if($this->request->param('p_id')){
           $Person_info = $modelPerson->findOrEmpty($this->request->param('p_id'));
           $res = $Person_info->save($data);
       }else{
           $res = $modelPerson->save($data);
       }
       return json($this->sore($res));


   }

   public function delPerson(){

       $modelWxappGame = new \app\admin\model\HotPerson();
       $WxappGame_info =  $modelWxappGame->findOrEmpty($this->request->param('s_id'));
       if($WxappGame_info->isEmpty()){
           return json($this->merr('该游戏不存在!'));
       }else{
           $res = $WxappGame_info->delete();
       }
       return json($this->sore($res));
   }

   public function image_upload(){
       $file = $this->request->file('file');
       $savename = Filesystem::disk('public')->putFile( '/uploads/image', $file);
       $url = 'storage/'.$savename;
       return json($this->suc(['src'=>$url,'title'=>'']));
   }





}