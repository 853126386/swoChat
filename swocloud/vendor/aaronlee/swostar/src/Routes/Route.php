<?php
namespace SwoStar\Routes;

use SwoStar\Message\Http\Request as SwooleRequest;
use SwoStar\Console\Input;
class Route{
    //单例
    protected static $instance;

    //路由容器
    protected   $routes = [];

    //访问类型
    protected  static  $verbs = ['GET','POST','PUT','PATHC','DELETE'];

    //路有文件地址
    protected $routeMap=[];

    //请求方法
    protected $method=null;
    //服务类型
    protected $flag=null;

    protected function __construct()
    {
        $this->routeMap = [
            'Http' => app()->getBasePath().'/route/http.php',
            'WebSocket' => app()->getBasePath().'/route/web_socket.php',
        ];
    }

    /**
     * 匹配路由并执行请求
     */
    public function match($path, $param = [])
    {
        /*
        本质就是一个字符串的比对
        1. 获取请求的uripath
        2. 根据类型获取路由
        3. 根据请求的uri 匹配 相应的路由；并返回action
        4. 判断执行的方法的类型是控制器还是闭包
           4.1 执行闭包
           4.2 执行控制器
        */
        $action = null;
        foreach ($this->routes[$this->flag][$this->method] as $uri => $value) {
            $uri = ($uri && substr($uri,0,1)!='/') ? "/".$uri : $uri;

            if ($path === $uri) {
                $action = $value;
                break;
            }
        }

        if (!empty($action)) {
            return $this->runAction($action, $param);
        }

        Input::info('没有找到方法');

        return "404";

        // 要求就是websocket的回调时间怎么运用到不同控制器中
        // websocket 配合与路由
    }

    private function runAction($action, $param = null)
    {
        if ($action instanceof \Closure) {
            return $action(...$param);
        } else {
            // 控制器解析
            $namespace = "\App\\".$this->flag."\Controller\\";

            // IndexController@dd
            $arr = \explode("@", $action);
            $controller = $namespace.$arr[0];
            $class = new $controller();
//            return $class->{$arr[1]}(...$param);
            return call_user_func([$class,$arr[1]],...$param);
        }
    }



    /**
     * websocket路由添加
     * @param $uri
     * @param $action
     * @return $this
     */
    public function wsController($uri,$controller)
    {
        $actions=['open','message','close'];
        foreach ($actions as $action){
            $this->addRoute([$action],$uri,$controller.'@'.$action);
        }
    }

    public function get($uri,$action)
    {
        return $this->addRoute(['GET'],$uri,$action);

    }

    public function post($uri,$action)
    {
        return $this->addRoute(['POST'],$uri,$action);
    }

    public function any($uri, $action)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }
    /**
     * 注册路由
     * @param $methods
     * @param $uri
     * @param $action
     * @return $this
     */
    protected function addRoute($methods,$uri,$action)
    {
        foreach ($methods as  $method){
            $this->routes[$this->flag][$method][$uri]=$action;
        }
        return $this;
    }

    /**
     * 注册路由
     * @return $this
     */
    public function registerRoute()
    {
        foreach ($this->routeMap as $key => $path){
            $this->flag = $key;
            require_once $path;
        }
        return $this;
    }


    /**
     * @param mixed $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }


    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance=new static();    
        }
        return self::$instance;
    }

    /**
     * 设置服务类型 flag
     * @param $flag
     * @return $this
     */
    public function setFlag($flag)
    {
        $this->flag=$flag;
        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * 设置请求方法
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method=$method;
        return $this;
    }



}
