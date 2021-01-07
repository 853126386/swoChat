<?php
namespace SwoCloud;

use Swoole\Coroutine\Http\Client;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use SwoCloud\Supper\Arithmetic;
use Firebase\JWT\JWT;
use Redis;
class Route extends Server {

    protected $serverKey='im_server';
    protected $redis=null;
    protected $dispatcher=null;

    public function onWorkerStart(SwooleServer $server, int $workerId){
        $this->redis = new Redis;
        $this->redis->pconnect("172.19.0.3", 6379);
    }

    /**
     * 初始化配置
     */
    protected  function initSetting(){
        $this->host = '0.0.0.0';
        $this->port = '9002';
    }

    protected function createServer()
    {
        $this->swooleServer=new SwooleWebSocketServer($this->host,$this->port);
    }


    protected function initEvent()
    {
        $this->setEvent('sub',[
            'request'   =>  'onRequest',
            'open'   =>  'onOpen',
            'message'   =>  'onMessage',
            'close'   =>  'onClose',
        ]);
    }



    /**
     * 监听WebSocket连接打开事件
     * @param SwooleServer $server
     * @param SwooleRequest $request
     */
    public function onOpen(SwooleServer $server,  $request)
    {
        // 需要获取访问的地址？

    }

    /**监听WebSocket消息事件
     * @param SwooleServer $server
     * @param $frame
     */
    public function onMessage(SwooleServer $server, $frame) {

        $data=json_decode($frame->data,true);
        $method=$data['method'];
        $fd=$frame->fd;
        $this->getDispatcher();

        dd($method);
        //调用派遣器
        call_user_func_array([$this->getDispatcher(),$method],[$this,$server,$fd,$data]);
    }


    /**
     * 监听WebSocket连接关闭事件
     */
    public function onClose($ser, $fd)
    {
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response)
    {
        if($request->server['request_uri']=='/favicon,ico'){
            $response->end();
            return null;
        }
        // 解决跨域
        $response->header('Access-Control-Allow-Origin', "*");
        $response->header('Access-Control-Allow-Methods', "GET,POST");
        $method=$request->post['method'];
        call_user_func_array([$this->getDispatcher(),$method],[$this,$request,$response]);
    }

    public function getRedis()
    {
        return $this->redis;
    }

    public function getDispatcher()
    {
        if(empty($this->dispatcher)){
            $this->dispatcher=new Dispatcher();
        }
        return $this->dispatcher;
    }

    public function getServerKey(){
        return $this->serverKey;
    }

    /**
     * 获取所有存活的im-server
     * @return mixed
     */
    public function getAllIMServer()
    {
        return $this->redis->Smembers($this->getServerKey());;
    }

    /**
     * 重所有的im-server中获取一台
     * @return mixed|null
     */
    public function getIMServer()
    {
        $IMServers=$this->getAllIMServer();
        if(!empty($IMServers)){
            //轮训算法获取
            return Arithmetic::round($IMServers);
        }

        return null;
    }

    /**
     * 生成token
     * @param $uid
     * @param $serverUrl
     * @return string
     */
    public function getToken($uid,$serverUrl)
    {
        $key='swocloud';
        $time=time();
        $payload=[
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => $time, // 签发时间
            "nbf" => $time,  // 生效时间
            "exp" => $time + (60 * 60 * 24),
            "data"=>[
                "uid"=>$uid,
                "name"=>"aaronlee_".$uid,
                "serverUrl"=>$serverUrl
            ]
        ];
        return JWT::encode($payload,$key);
    }

    /**
     * 发送信息
     * @param $ip
     * @param $port
     * @param $data
     * @param $header
     */
    public function send($ip,$port,$data,$header)
    {
        //创建协程客户端
        $client=new Client($ip,$port);
        //添加请求头
        empty($header)?:$client->setHeaders($header);

        if($client->upgrade('/')){
            $client->push(json_encode($data));
        }


    }
}