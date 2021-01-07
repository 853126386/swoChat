<?php
namespace app\Listener;

use SwoStar\Event\Listener;
use SwoStar\Server\Websocket\WebsocketServer;
use Swoole\Server as SwooleServer;
use Swoole\Coroutine\Http\Client;
use SwoStar\Server\WebSocket\Connections;
class WSMessageFrontListener extends Listener{

    protected $name='ws.front';

    public function handler(WebsocketServer $websocketServer=null,SwooleServer $swooleServer=null,$frame=null)
    {
        //回调接受到的方法
        $data=json_decode($frame->data,true);
//        call_user_func([$this,$data['method']],[$websocketServer,$swooleServer,$data,$frame->fd]);

        $this->{$data['method']}($websocketServer, $swooleServer ,$data, $frame->fd);
    }

    /**
     * 服务器广播，将接收到的信息发送到路由服务
     * @param WebsocketServer|null $websocketServer
     * @param SwooleServer|null $swooleServer
     * @param null $data
     * @param null $fd
     */
    public function serverBroadcast(WebsocketServer $websocketServer,SwooleServer $swooleServer,$data,$fd){
        $config=$this->app->make('config');
        $client= new Client($config->get('server.route.server.ip'),$config->get('server.route.server.port') );
        if($client->upgrade('/')){
               $client->push(json_encode([
                   'method' =>'routeBroadcast',
                   'msg'=>$data['msg']
               ]));
           }


    }

    /**
     * 接受route服务器的广播信息
     * @param WebsocketServer|null $websocketServer
     * @param SwooleServer|null $swooleServer
     * @param null $data
     * @param null $fd
     */
    protected function routeBroadcast(WebsocketServer $websocketServer=null,SwooleServer $swooleServer=null,$data=null,$fd=null){
        dd($data, 'server 中的 routeBroadcast');
        $websocketServer->sendAll(json_encode($data['msg']));
    }

    /**
     * 接收客户端私聊的信息
     *
     * 六星教育 @shineyork老师
     * @param  WebSocketServer $swoStarServer [description]
     */
    protected function privateChat(WebSocketServer $swoStarServer, $swooleServer ,$data, $fd)
    {
        // 1. 获取私聊的id
        $clientId = $data['clientId'];
        // 2. 从redis中获取对应的服务器信息
        $clientIMServerInfoJson = $swoStarServer->getRedis()->hGet($this->app->make('config')->get('server.route.jwt.key'), $clientId);
        $clientIMServerInfo = json_decode($clientIMServerInfoJson, true);
        // 3. 指定发送
        $request = Connections::get($fd)['request'];
        $token = $request->header['sec-websocket-protocol'];
        // $url = 0.0.0.0:9000
        $clientIMServerUrl = explode(":", $clientIMServerInfo['serverUrl']);
        $swoStarServer->send($clientIMServerUrl[0], $clientIMServerUrl[1], [
            'method' => 'forwarding',
            'msg' => $data['msg'],
            'fd' => $clientIMServerInfo['fd']
        ], [
            'sec-websocket-protocol' => $token
        ]);
    }
    /**
     * 转发私聊信息
     *
     * 六星教育 @shineyork老师
     * @param  WebSocketServer $swoStarServer [description]
     */
    protected function forwarding(WebSocketServer $swoStarServer, $swooleServer ,$data, $fd)
    {
        $swooleServer->push($data['fd'], json_encode(['msg' => $data['msg']]));
    }
}
