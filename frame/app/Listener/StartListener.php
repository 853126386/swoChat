<?php
namespace App\Listener;

use SwoPolaris\Event\Listener;
class StartListener extends Listener{

    protected $name='start';
    public function handler( $swoServer=null, $swooleServer=null)
    {
//        dd('我是start监听器');
        $config=app('config')->get('server');
        go(function ()use($config,$swoServer){
            $client=new \Swoole\Coroutine\Http\Client($config['route']['server']['ip'],$config['route']['server']['port']);
            if($client->upgrade('/')){
                $data=[
                    'method'=>'register',
                    'ip'=>$config['ws']['ip'],
                    'port'=>$config['ws']['port'],
                    'serverName'=>'server_im1',
                ];
                $client->push(json_encode($data));

//
                \Swoole\Timer::tick(3000,function () use($client){
                    $client->push('',WEBSOCKET_OPCODE_PING);
                });

            }
        });



    }


}
