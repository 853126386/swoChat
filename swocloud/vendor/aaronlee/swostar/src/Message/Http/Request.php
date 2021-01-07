<?php
namespace SwoStar\Message\Http;

use Swoole\Http\Request as SwooleRequest;
use SwoStar\Message\Request as baseRequest;

class Request extends baseRequest{


    protected $swooleRequest = null;

    protected $method = null ;

    protected $uriPath =null;

    protected $server = null;


    /**
     *
     * @param SwooleRequest $swooleRequest
     * @return mixed
     */
    public static function init(SwooleRequest $swooleRequest){

        $self=app('httpRequest');

        $self->swooleRequst=$swooleRequest;

        $self->server=$swooleRequest->server;
        $self->method=$swooleRequest->server['request_method']??'';
        $self->uriPath=$swooleRequest->server['request_uri']??'';
        return $self;
    }


    public function getMethod()
    {
        return $this->method;
    }

    public function getUriPath()
    {
        return $this->uriPath;
    }

}