<?php


namespace app\wxapp\logic;

use app\wxapp\model\Wxapp;
use think\facade\App;
class ConfigWxappLogic {

    public function getConfig($wxapp_id){
        $model_wxapp = new Wxapp();
        $config = $model_wxapp->findOrEmpty($wxapp_id);
        if($config->isEmpty()){
            return false;
        }else{
            return [
                'app_id' => $config['appid'],
                'secret' => $config['key'],
                'token'  => $config['xcx_token'],
                'aes_key'=> $config['encodingaeskey'],
                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file' => App::getRuntimePath().'mini_'.$wxapp_id.'.log',
                ],
            ];
        }
    }

}