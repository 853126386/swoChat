<?php
namespace SwoStar\Server\Websocket;

use SwoStar\Server\Http\HttpServer ;

use Swoole\Websocket\Server as SwooleServer;
use Swoole\Http\Request as Request;
use Swoole\Http\Response  as Response;

class WebsocketServer extends HttpServer{

    protected  function initSetting(){
        $config=app('config');
        $this->host = $config->get('server.ws.host');
        $this->port = $config->get('server.ws.port');
        $this->config = $config->get('server.ws.swoole');
    }

    protected function createServer()
    {
        $this->swooleServer=new SwooleServer($this->host,$this->port);
    }

    protected function initEvent()
    {
        $event=[
            'request'   =>  'onRequest',
            'open'   =>  'onOpen',
            'message'   =>  'onMessage',
            'close'   =>  'onClose',
        ];
        // 判断是否自定义握手的过程
        ( ! $this->app->make('config')->get('server.ws.is_handshake'))?: $event['handshake'] = 'onHandShake';

        $this->setEvent('sub', $event);
    }

    /**
     * 自定义握手
     * @param Request $request
     * @param Response $response
     */
    public function onHandShake(Request $request, Response $response)
    {
        $this->app->make('event')->trigger('ws.hand', [$this, $request, $response]);

        $this->onOpen($this->swooleServer,$request);
    }
    

    /**
     * 监听WebSocket连接打开事件
     * @param SwooleServer $server
     * @param SwooleRequest $request
     */
    public function onOpen(SwooleServer $server,  $request)
    {
        // 需要获取访问的地址？
        Connections::init($request->fd, $request);

        app('route')->setFlag('WebSocket')->setMethod('open')->match($request->server['path_info'],[$server,$request]);
    }

    /**监听WebSocket消息事件
     * @param SwooleServer $server
     * @param $frame
     */
    public function onMessage(SwooleServer $server, $frame) {
        $path = (Connections::get($frame->fd))['path'];
        app('route')->setFlag('WebSocket')->setMethod('message')->match($path, [$server, $frame]);

        app('event')->trigger('ws.front',[$this,$server,$frame]);
    }

    /**
     * 监听WebSocket连接关闭事件
     */
    public function onClose($ser, $fd)
    {
        $path = (Connections::get($fd))['path'];
         app('route')->setFlag('WebSocket')->setMethod('close')->match($path, [$ser, $fd]);

        app('event')->trigger('ws.close',[$this,$fd]);
        dd('断开连接');

    }

    /**
     *给所有连接发送信息
     * @param  [type] $msg [description]
     * @return [type]      [description]
     */
    public function sendAll($msg)
    {
        dd('sendAll...');
        foreach ($this->swooleServer->connections as $key => $fd) {
            if ($this->swooleServer->exists($fd)) {
                $this->swooleServer->push($fd, $msg);
            }
        }
    }



}