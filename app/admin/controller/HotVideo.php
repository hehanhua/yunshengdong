<?php


namespace app\admin\controller;


use app\admin\BaseController;
use think\facade\Filesystem;


class HotVideo extends BaseController {

    public function video_list(){
        $modelVedio = new \app\admin\model\HotVideo();
        $list = $modelVedio->select();
        return json($this->suc($list));
    }

   public function addVideo(){
       $modelVedio = new \app\admin\model\HotVideo();
       $data = $this->request->param();
       if($this->request->param('v_id')){
           $Person_info = $modelVedio->findOrEmpty($this->request->param('v_id'));
           $res = $Person_info->save($data);
       }else{
           $res = $modelVedio->save($data);
       }
       return json($this->sore($res));


   }

   public function delPerson(){

       $modelWxappGame = new \app\admin\model\HotVideo();
       $WxappGame_info =  $modelWxappGame->findOrEmpty($this->request->param('s_id'));
       if($WxappGame_info->isEmpty()){
           return json($this->merr('该游戏不存在!'));
       }else{
           $res = $WxappGame_info->delete();
       }
       return json($this->sore($res));
   }

   public function video_upload(){
       $file = $this->request->file('layuiVideo');
       $savename = Filesystem::disk('public')->putFile( '/uploads/video', $file);
       $url = 'storage/'.$savename;
       return json($this->suc(['src'=>$url,'title'=>'']));
   }



}