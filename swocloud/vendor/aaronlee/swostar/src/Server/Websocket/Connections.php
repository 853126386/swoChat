<?php

namespace SwoStar\Server\WebSocket;

class Connections {

    private static $connections=[];


    /**
     * 保存客户端的连接，和请求
     * @param $fd
     * @param $request
     */
    public static function init($fd,$request){
        self::$connections[$fd]['path'] = $request->server['path_info'];
        self::$connections[$fd]['request'] = $request;
    }

    public static function get($fd=null)
    {
        if($fd==null)return false;

        return self::$connections[$fd]??null;
    }

    public static function del($fd=null)
    {
        if($fd==null)return false;

        if(isset(self::$connections[$fd])){
            unset(self::$connections[$fd]);
            return true;
        }
        return false;
    }

}
