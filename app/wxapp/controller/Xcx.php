<?php


namespace app\wxapp\controller;


use app\wxapp\BaseController;
use app\wxapp\logic\ConfigWxappLogic;
use app\wxapp\model\WxappGame;
use EasyWeChat\Factory;
use GuzzleHttp\Client;
use think\facade\Cache;
use think\facade\Db;


class Xcx extends BaseController {

    public function get_status(){
        $model_wxapp =  new \app\wxapp\model\Wxapp();
        $temp = $model_wxapp->where('appid',$this->request->param('appid'))->hidden(['token',''])->findOrEmpty();
        if($temp->isEmpty()){
            return json(['data'=>0]);
        }else{
            if($temp['bind_temp']==0){
                return json(['data'=>0]);
            }else{
                return json($this->suc(1));
            }

        }

    }
    //游戏狗
    //type_top = ad
    //type_one = recommend
    //type_two = hot
    //type_three = 卡牌比卡超
    //type_four = 经营
    //type_five = 三国
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

    public function get_gameid(){

        $wxapp_game_id = $this->request->param('s_id');

        Cache::set("wxapp_game_id", $wxapp_game_id);
    }



    public function game_url()
    {
        $code = $this->request->param('code');
        $model_wxapp = new \app\wxapp\model\Wxapp();
        $wxapp_id = $model_wxapp->where('appid', $this->request->param('appid'))->value('wxapp_id');
        $channel_id = $model_wxapp->where('appid', $this->request->param('appid'))->value('channel_id');
        $logic = new ConfigWxappLogic();
        $config = $logic->getConfig($wxapp_id);
        $app = Factory::miniProgram($config);
        $response = $app->server->serve();
		//$response->send();
        $message = $app->server->getMessage();
        $wxapp_game_id = Cache::get("wxapp_game_id");
        if ($this->request->param('openid')) {
            $openid = $this->request->param('openid');
            if ($message['MsgType'] == 'event' || $message['MsgType'] == 'text'|| $message['MsgType']=='miniprogrampage') {
                $model_WxappGame = new WxappGame();
                $game_res = $model_WxappGame->where('s_id', $wxapp_game_id)->find()->toArray();
                $app->customer_service->send([
                    'touser' => $openid,
                    'msgtype' => 'link',
                    'link' => [
                        "title" => $game_res['game_name'],
                        "description" => $game_res['desc'],
                        //http://www.shandw.com/mi/game/2038606029.html?channel=13539
                        "url" => 'http://www.shandw.com/mi/game/'.$game_res['game_id'].'.html?channel='.$channel_id,
                        "thumb_url" => $game_res['game_icon'],
                    ]
                ]);

//                $app->customer_service->send([
//                    'touser' => $openid,
//                    'msgtype' => 'link',
//                    'link' => [
//                        "title" => 'APP下载链接',
//                        "description" => '下载APP，绝对不迷路',
//                        "url" => 'http://jf1999.hjygame.com/sdk.php/?ac=appdesc&code=2297',
//                        "thumb_url" => $game_res['game_icon'],
//                    ]
//                ]);
            }

        } else if (Cache::has("open_id" . $code)) {
            $openid = Cache::get("open_id" . $code);
            if  ($message['MsgType'] == 'event' || $message['MsgType'] == 'text'|| $message['MsgType']=='miniprogrampage') {
                $model_WxappGame = new WxappGame();
                $game_res = $model_WxappGame->where('s_id', $wxapp_game_id)->find()->toArray();
                $app->customer_service->send([
                    'touser' => $openid,
                    'msgtype' => 'link',
                    'link' => [
                        "title" => $game_res['game_name'],
                        "description" => $game_res['desc'],
                        "url" => 'http://www.shandw.com/mi/game/'.$game_res['game_id'].'.html?channel='.$channel_id,
                        "thumb_url" => $game_res['game_icon'],
                    ]
                ]);
//                $app->customer_service->send([
//                    'touser' => $openid,
//                    'msgtype' => 'link',
//                    'link' => [
//                        "title" => 'APP下载链接',
//                        "description" => '下载APP，绝对不迷路',
//                        "url" => 'http://jf1999.hjygame.com/sdk.php/?ac=appdesc&code=2297',
//                        "thumb_url" => $game_res['game_icon'],
//                    ]
//                ]);
            }
        }else {
                $data = [
                    'grant_type' => 'authorization_code',
                    'appid' => $config['app_id'],
                    'secret' => $config['secret'],
                    'js_code' => $code,
                ];
                $url = 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($data);
                $client = new Client([
                    'timeout' => 6.0,
                ]);
                $response = $client->get($url);
                $str = $response->getBody()->getContents();
                $arr = json_decode($str, true);
                $message = $app->server->getMessage();
                $openid = $arr['openid'];
                Cache::set("open_id" . $code, $openid);
                $model_WxappGame = new WxappGame();
                $game_res = $model_WxappGame->where('s_id', $wxapp_game_id)->find()->toArray();
                $app->customer_service->send([
                    'touser' => $openid,
                    'msgtype' => 'link',
                    'link' => [
                        "title" => $game_res['game_name'],
                        "description" => $game_res['desc'],
                        "url" => 'http://www.shandw.com/mi/game/'.$game_res['game_id'].'.html?channel='.$channel_id,
                        "thumb_url" => $game_res['game_icon'],
                    ]
                ]);
//                $app->customer_service->send([
//                    'touser' => $openid,
//                    'msgtype' => 'link',
//                    'link' => [
//                        "title" => 'APP下载链接',
//                        "description" => '下载APP，绝对不迷路',
//                        "url" => 'http://jf1999.hjygame.com/sdk.php/?ac=appdesc&code=2297',
//                        "thumb_url" => $game_res['game_icon'],
//                    ]
//                ]);
            }


        }


}

