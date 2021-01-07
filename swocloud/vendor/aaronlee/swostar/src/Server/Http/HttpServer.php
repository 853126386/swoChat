<?php


namespace SwoStar\Server\Http;

use SwoStar\Routes\Route;
use SwoStar\Server\Server;
use Swoole\Http\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

use SwoStar\Message\Http\Request as Request;
class HttpServer extends Server{

    protected  function initSetting(){
        $config=app('config');
        $this->host = $config->get('server.http.host');
        $this->port = $config->get('server.http.port');
    }

    protected function createServer()
    {
        $this->swooleServer=new SwooleServer($this->host,$this->port);
    }


    protected function initEvent()
    {
        $this->setEvent('sub',[
            'request'   =>  'onRequest'
        ]);
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response)
    {
        $uri=$request->server['request_uri'];
        if($uri == '/favicon.ico'){
            $response->status(404);
            $response->end('');
        }
        $httpRequest=Request::init($request);
        $return = app('route')->setFlag('Http')->setMethod($httpRequest->getMethod())->match($httpRequest->getUriPath());

//        dd($httpRequest->getMethod(), "Method");
//        dd($httpRequest->getUriPath(), "UriPath");
        $response->end($return);
    }

}