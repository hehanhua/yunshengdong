<?php


namespace app\wxapp\controller;


use app\wxapp\BaseController;
use app\wxapp\logic\ConfigWxappLogic;
use app\wxapp\model\WxappGame;
use EasyWeChat\Factory;
use GuzzleHttp\Client;
use think\facade\Cache;
use think\facade\Db;


class Wxapp extends BaseController {

    public function is_bind(){
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $temp = $model_wxapp->where('wxapp_id',$this->request->param('wxapp_id'))->value('bind_temp');
        if(empty($temp)){
            return json($this->merr());
        }else{
            $ck_wxapp = rand(10,100);
            $data = [
              'ck_wxapp' =>$ck_wxapp,
              'url' =>'https://v1.j6yx.com/wxapp/wxapp/getGame.html',
            ];

            return json($this->suc($data));
        }

    }

    public function is_bind_v2(){

        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $temp = $model_wxapp->where('appid',$this->request->param('appid'))->hidden(['token',''])->findOrEmpty();
        if($temp->isEmpty()){
            return json($this->merr());
        }else{
            if($temp['bind_temp']==0){
                return json($this->merr());
            }else{
                $temp['share_image'] = cdnurl($temp['share_image']);
                return json($this->suc($temp));
            }

        }

    }

    public function getGame(){
        $model_wxapp = new \app\wxapp\model\Wxapp();
        $temp_id = $model_wxapp->where('wxapp_id',$this->request->param('wxapp_id'))->value('wxapp_temp_id');
        $model_game = new WxappGame();
        $hot_game = $model_game->where('wxapp_temp_id',$temp_id)->order('game_weight','desc')->limit(0,3)->select()->toArray();
        $game_list = $model_game->where('wxapp_temp_id',$temp_id)->order('game_weight','desc')->limit(2,100)->select()->toArray();
        foreach ($hot_game as $k=>$v){
            $hot_game[$k]['id_show_h5'] = '1';
            $hot_game[$k]['h5_reply_keyword'] = 1;
            $hot_game[$k]['id'] = $v['wxapp_game_id'];
            $hot_game[$k]['h5_wxapp_card'] = 'https://wx.j6yx.com/addons/qy_gamebox/resources/images/cursor.jpg';
            $hot_game[$k]['url_icon'] = cdnurl($v['url_icon']);
        }
        foreach ($game_list as $k=>$v){
            $game_list[$k]['id_show_h5'] = '1';
            $game_list[$k]['h5_reply_keyword'] = 1;
            $game_list[$k]['id'] = $v['wxapp_game_id'];
            $game_list[$k]['h5_wxapp_card'] = 'https://wx.j6yx.com/addons/qy_gamebox/resources/images/cursor.jpg';
            $game_list[$k]['url_icon'] = cdnurl($v['url_icon']);
        }
        $data =[
            'hot_list'=>$hot_game,
            'best_list'=>$game_list
        ];
        return json($this->suc($data));
    }


    public function box_home(){
        //获取轮播图
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $wxapp_temp_id = $model_wxapp->where('appid',$this->request->param('appid'))->value('wxapp_temp_id');
        $model_wxapp_temp = new \app\wxapp\model\WxappTemp();
        $wxapp_temp =  $model_wxapp_temp->where('wxapp_temp_id',$wxapp_temp_id)->find()->toArray();
        $model_wxapp_game = new \app\admin\model\WxappGame();
        $type_top =  $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_top')->limit(3)->select()->toArray();
        $type_one = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_one')->select()->toArray();
        $type_two = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_two')->select()->toArray();
        $type_three = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_three')->select()->toArray();
        $type_four = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_four')->select()->toArray();
        $type_five = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_five')->select()->toArray();

        $param = [
            'type_top' => $type_top,
            'type_one' => $type_one,
            'type_two' => $type_two,
            'type_three' => $type_three,
            'type_four' => $type_four,
            'type_five' => $type_five,
            'type_one_title' =>$wxapp_temp['type_one'] ,
            'type_two_title' =>$wxapp_temp['type_two'] ,
            'type_three_title' =>$wxapp_temp['type_three'] ,
            'type_four_title' =>$wxapp_temp['type_four'] ,
            'type_five_title' =>$wxapp_temp['type_five'] ,
        ];
        return json($this->suc($param));
    }


    public function type_data(){

        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $wxapp_temp_id = $model_wxapp->where('appid',$this->request->param('appid'))->value('wxapp_temp_id');
        $model_wxapp_game = new \app\admin\model\WxappGame();
        $game = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type',$this->request->param('type'))->select()->toArray();
        return json($this->suc($game));

    }

    public function game_url(){
        $wxapp_game_id = $this->request->param('wxapp_game_id');
        $code = $this->request->param('my_code');
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $wxapp_id = $model_wxapp->where('appid',$this->request->param('appid'))->value('wxapp_id');
        $channel_id = $model_wxapp->where('appid',$this->request->param('appid'))->value('channel_id');
        $logic = new ConfigWxappLogic();
        $config = $logic->getConfig($wxapp_id);
        $app = Factory::miniProgram($config);
        $response = $app->server->serve();
        if($this->request->param('echostr')){
            $response->send();exit;
        }
        Db::name('test')->insert([
            'name'=>10002,
            'value'=>\GuzzleHttp\json_encode($this->request->param()),
        ]);
        if($this->request->param('openid')){
            $openid = $this->request->param('openid');
            if(Cache::has("wxapp_game_id".$openid)){
                $wxapp_game_id = Cache::get("wxapp_game_id".$openid);
            }else{
                $wxapp_game_id = 1;
            }
        }else if(Cache::has("open_id".$code)){
            $openid = Cache::get("open_id".$code);
            Cache::set("wxapp_game_id".$openid,$wxapp_game_id);
        }else{
            $data =[
                'grant_type'=>'authorization_code',
                'appid'=>$config['app_id'],
                'secret'=>$config['secret'],
                'js_code'=>$code,
            ];
            $url = 'https://api.weixin.qq.com/sns/jscode2session?'.http_build_query($data);
            $client = new Client([
                'timeout'  => 6.0,
            ]);
            $response = $client->get($url);
            $str = $response -> getBody()->getContents();
            $arr = json_decode($str,true);
            $message = $app->server->getMessage();
            $openid = $arr['openid'];
            Cache::set("open_id".$code,$openid);
            Cache::set("wxapp_game_id".$openid,$wxapp_game_id);
        }
        $model_WxappGame =  new WxappGame();
        $game_res = $model_WxappGame->where('s_id',$wxapp_game_id)->find()->toArray();
        $app->customer_service->send([
            'touser' => $openid,
            'msgtype' => 'link',
            'link' => [
                "title" => $game_res['game_name'],
                "description" =>$game_res['desc'],
                "url" => 'http://v1.j6yx.com/wap/game/play.html?gid='.$game_res['game_id'].'&l=wx&fmid='.$channel_id,
                "thumb_url" => $game_res['game_icon'],
            ]
        ]);
    }

    public function api_get_index_data(){
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $wxapp_temp_id = $model_wxapp->where('appid',$this->request->param('appid'))->value('wxapp_temp_id');
        $wxapp_name = $model_wxapp->where('appid',$this->request->param('appid'))->value('wxapp_name');
        $model_wxapp_temp = new \app\wxapp\model\WxappTemp();
        $wxapp_temp =  $model_wxapp_temp->where('wxapp_temp_id',$wxapp_temp_id)->find()->toArray();
        $model_wxapp_game = new \app\admin\model\WxappGame();
        $type_top =  $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_top')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $type_one = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_one')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $type_two = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_two')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $type_three = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_three')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $type_four = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_four')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $type_five = $model_wxapp_game->where('wxapp_temp_id',$wxapp_temp_id)->order('game_weight','desc')->where('type','type_five')->field('s_id,game_name,game_icon,slide_icon')->select()->toArray();
        $param = [
            'ad_list' => $type_top,
            'xcx_name' => $wxapp_name,
            'recommend_list' => $type_one,
            'hot_list' => $type_two,
            'kapai_list' => $type_three,
            'jingying_list' => $type_four,
            'sanguo_list' => $type_five,
            'recommend_list_title' =>$wxapp_temp['type_one'] ,
            'hot_list_title' =>$wxapp_temp['type_two'] ,
            'kapai_list_title' =>$wxapp_temp['type_three'] ,
            'jingying_list_title' =>$wxapp_temp['type_four'] ,
            'sanguo_list_title' =>$wxapp_temp['type_five'] ,
        ];
        return json($this->suc($param));
    }

    public function get_status(){
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $temp = $model_wxapp->where('appid',$this->request->param('appid'))->hidden(['token',''])->findOrEmpty();
        if($temp->isEmpty()){
            return json($this->suc(0));
        }else{
            if($temp['bind_temp']==0){
                return json($this->suc(0));
            }else{
                return json($this->suc(1));
            }

        }

    }

}

