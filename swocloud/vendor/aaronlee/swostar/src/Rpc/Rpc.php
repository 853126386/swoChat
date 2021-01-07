<?php
namespace SwoStar\Rpc;

use Swoole\Server as SwooleServer;
class Rpc{

    public function __construct(SwooleServer $swooleServer,$config)
    {
        $port=$swooleServer->listen($config['host'],$config['port'],SWOOLE_SOCK_TCP);

        $port->on('connect', [$this, 'connect']);
        $port->on('receive', [$this, 'receive']);
        $port->on('close', [$this, 'close']);
    }
    //设置每个port的回调函数
    public function connect($serv, $fd){
        echo "Client:Connect.\n";
    }

    public function receive($serv, $fd, $from_id, $data) {
        $serv->send($fd, 'Swoole: '.$data);
        $serv->close($fd);
    }

    public function close($serv, $fd) {
        echo "Client: Close.\n";
    }

    public function packet($serv, $data, $addr) {
        var_dump($data, $addr);
    }




}