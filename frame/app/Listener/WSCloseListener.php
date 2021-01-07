<?php
namespace app\Listener;

use Firebase\JWT\JWT;
use SwoStar\Event\Listener;
use SwoStar\Server\Websocket\WebsocketServer;
use SwoStar\Server\Websocket\Connections;
class WSCloseListener extends Listener{


    protected $name='ws.close';

    public function handler(WebsocketServer $webServer=null,$fd=null)
    {
//        Connections::del($fd);
        $config=$this->app->make('config');

        //客户端握手请求
        $request=Connections::get($fd)['request'];

        //获取客户端token
        $token=$request->header['sec-websocket-protocol'];

        $jwtKey=$config->get('server.route.jwt.key');

        //对token进行jwt解析
        $jwt=JWT::decode($token,$jwtKey,$config->get('server.route.jwt.alg'));

        dd($jwt->data->uid,'关闭客户端');
        //删除该客户端在redis中的数据
        $webServer->getRedis()->hdel($jwtKey,$jwt->data->uid);

        //删除该客户端在连接类中保存的数据
        Connections::del($fd);
    }


}