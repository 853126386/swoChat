<?php
namespace SwoCloud;
use Swoole\Server as SwooleServer;
class Dispatcher{

    /**
     * server注册到Route中
     * @param Route $route
     * @param SwooleServer $server
     * @param $fd
     * @param $data
     */
    public function register(Route $route,SwooleServer $server,$fd,$data)
     {
        $serverKey=$route->getServerKey();
        $redis=$route->getRedis();
        $value=json_encode([
          'ip'=>$data['ip'],
          'port'=>$data['port'],
        ]);

        $redis->sadd($serverKey,$value);

         //建立定时器，定时判断连接是否存在
         \Swoole\Timer::tick(3000, function($timer_id,$redis,SwooleServer $server,$serverKey,$fd,$value) {
             // 判断服务器是否正常运行，如果不是就主动清空
             // 并把信息从redis中移除
             if (!$server->exist($fd)) {
                 $redis->srem($serverKey, $value);
                 $server->clearTimer($timer_id);
                 dd('im server 宕机， 主动清空');
             }
         }, $redis, $server, $serverKey, $fd, $value);
     }

    /**
     * 用户登入登入获取token
     * @param Route $route
     * @param $request
     * @param $response
     */
    public function login(Route $route,$request,$response)
    {
        //--------------这个uid为模拟用户id，可自行修改登入认证逻辑------------
        $uid=$request->post['id'];
        //--------------------------------------------------------------

        $imServer=json_decode($route->getIMServer(),true);

        $serverUrl=$imServer['ip'].':'.$imServer['port'];

        $token=$route->getToken($uid,$serverUrl);

        $response->end(json_encode(['token'=>$token,'url'=>$serverUrl]));
    }

    /**
     * Route向服务器广播
     * @param Route $route
     * @param SwooleServer $server
     * @param $fd
     * @param $data
     */
    public function routeBroadcast(Route $route,SwooleServer $server,$fd,$data)
    {
        $IMServers=$route->getAllIMServer();

        $token=$route->getToken(0,$route->getHost().':'.$route->getPort());
        $header = ['sec-websocket-protocol' => $token];
        foreach ($IMServers as $im){
            $im=json_decode($im,true);
            $route->send($im['ip'],$im['port'],[
                'method'=>'routeBroadcast',
                'msg'=>$data['msg']
            ],$header);
        }
    }



}
