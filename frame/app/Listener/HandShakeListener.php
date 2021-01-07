<?php
namespace App\Listener;

use Firebase\JWT\JWT;
use SwoStar\Event\Listener;
use SwoStar\Server\Websocket\WebsocketServer  ;
use Swoole\Http\Request;
use Swoole\Http\Response ;
class HandShakeListener extends Listener{

    protected $name='ws.hand';



    public function handler(WebSocketServer $server = null, Request  $request = null, Response $response = null)
    {

        $token=$request->header['sec-websocket-protocol'];

        if(empty($token)||!($this->check($server,$token,$request->fd))){
            $response->end();
            return null;
        }

        $this->handshake($request,$response);

    }

    /**
     * 验证token，并保存用户连接
     * @param $server
     * @param $token
     * @param $fd
     * @return bool
     */
    protected function check($server,$token,$fd){
        try{
            $config = $this->app->make('config');
            $key = $config->get('server.route.jwt.key');
            // 1. 进行jwt验证
            $jwt = JWT::decode($token, $key, $config->get('server.route.jwt.alg'));


            $userInfo=$jwt->data;
            //将用户连接保存到redis中
            $server->getRedis()->hset($key,$userInfo->uid,json_encode([
                'fd'=>$fd,
                'name'=>$userInfo->name,
                'serverUrl' => $userInfo->serverUrl
            ]));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * 自定义握手
     * @param Request|null $request
     * @param Response|null $response
     * @return false
     */
    protected function handshake( Request  $request = null, Response $response = null)
    {
        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );

        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        $response->status(101);
        $response->end();
    }
}