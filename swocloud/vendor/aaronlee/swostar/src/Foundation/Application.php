<?php
namespace SwoStar\Foundation;

use SwoStar\Event\Event;
use SwoStar\Routes\Route;
use SwoStar\Server\Http\HttpServer;

use SwoStar\Container\Container;
use SwoStar\Server\Websocket\WebsocketServer;

class Application extends  Container{

    protected const SWOSTAR_WELCOME = "
      _____                     _____     ___
     /  __/             ____   /  __/  __/  /__   ___ __    __  __
     \__ \  | | /| / / / __ \  \__ \  /_   ___/  /  _`  |  |  \/ /
     __/ /  | |/ |/ / / /_/ /  __/ /   /  /_    |  (_|  |  |   _/
    /___/   |__/\__/  \____/  /___/    \___/     \___/\_|  |__|
    ";

    protected   $basePath = '';

    /**
     * 注册基础绑定
     */
    protected function registerBaseBindings(){
        self::setInstance($this);
        $binds = [
            // 标识  ， 对象
            'httpRequest' => (new \SwoStar\Message\Http\Request()),
            'config' => (new \SwoStar\Config\Config()),
        ];
        foreach ($binds as $key => $value){
            $this->bind($key,$value);
        }

    }


    public function __construct($path = null)
    {
        if(!empty($path)){
            $this->setBsePath($path);
        }
        $this->registerBaseBindings();
        $this->init();
//        dd(app('event')->getEvents());
        echo self::SWOSTAR_WELCOME;
    }

    /**
     * 初始化：
     * 1：绑定路由
     */
    protected function init()
    {
        $this->bind('route',Route::getInstance()->registerRoute());
        $this->bind('event',$this->registerEvent());
    }

    protected function registerEvent()
    {

        $Event=new Event();
        $eventPath = $this->getBasePath() . '/app/Listener';

        $files = scandir($eventPath);

        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $class="\\App\Listener\\".explode('.',$file)[0];
            //将监听器注册到事件中
            if(class_exists($class)){
                $listener=new $class($this);
                $event=$listener->getName();
                $Event->register($event,[$listener,'handler']);
            }
        }
        return $Event;
    }

    public function run($arg)
    {
        $server = null;
        switch ($arg[1]){
            case 'http:start':
                $server=new HttpServer($this);
                break;
            case 'ws:start':
                $server=new WebsocketServer($this);
                break;
            default:
                echo '亲输入您想启动的服务';
                return ;
        }
        $server->start();
    }

    protected function setBsePath($path = null)
    {
        $this->basePath = \rtrim($path , '\/');
    }

    public function getBasePath()
    {
        return $this->basePath;
    }





}