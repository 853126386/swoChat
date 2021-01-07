<?php
namespace App\WebSocket\Controller;

/**
 *
 */
class IndexController
{
    public function open($server, $request)
    {
        dd('indexController open');
    }
    public function message($server, $frame)
    {

        dd($frame->data);
//        debug_log( $frame->data);
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }
    public function close($ser, $fd)
    {
        dd('客户端断开连接');
    }
}
