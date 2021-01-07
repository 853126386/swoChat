<?php

namespace SwoStar\Server;

use Swoole\Server as SwooleServer;
use SwoStar\Foundation\Application;
use SwoStar\Rpc\Rpc;
use Redis;
abstract class Server{

    /**
     *  SwoStar server
     * @var Server|HttpServer|WebSocketServer
     */
    protected $swooleServer;

    protected $app;
    /**
     * 服务类型
     * @var string
     */
    protected $serverType = 'Tcp';


    /**
     * 监听地址
     * @var string
     */
    protected $host = "0.0.0.0";

    /**
     * 监听端口
     * @var int
     */
    protected $port = 9001;

    /**
     * 配置
     * @var int[]
     */
    protected $config = [
        'task_worker_num' => 0,
    ];

    /**
     * 用于记录pid的信息
     * @var array
     */
    protected $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids'   => []
    ];

    protected $redis=null;
    /**
     * 监听事件
     * @var array[]
     */
    protected $event=[


         // 所有服务公用监听事件
        'events'=>[
            'start' =>  'onStart',
            'managerStart' =>  'onManagerStart',
            'managerStop' =>  'onManagerStop',
            'shutDown' =>  'onShutDown',
            'workerStart' =>  'onWorkerStart',
            'workerStop' =>  'onWorkerStop',
            'workerError' =>  'onWorkerError',
        ],

        //子类监听事件
        'sub'=>[],

        //扩展回调函数
        'ext'=>[],
    ];

    public function __construct(Application $app)
    {
        $this->app =$app;
        //设置配置
        $this->initSetting();
        //创建server
        $this->createServer();

        //设置需要的回调函数
        $this->initEvent();
        //设置swoole的回调函数
        $this->setSwooleEvent();
    }

    /**
     * 初始化设置
     * @return mixed
     */
    protected abstract function initSetting();

    /**F
     *
     * 创建一个服务
     * @return mixed
     */
    protected abstract function createServer();

    /**
     *初始化监听的事件
     * @return mixed
     */
    protected abstract function initEvent();


    /**
     *启动
     */
    public function start()
    {

        $config = app('config');
        // 2. 设置配置信息
        $this->swooleServer->set($this->config);
        if ($config->get('server.http.tcpable')) {
            new Rpc($this->swooleServer, $config->get('server.http.rpc'));
        }

        //启动
        $this->swooleServer->start();
    }

    /**
     * 注册回调函数
     */
    protected function setSwooleEvent(){
        foreach ($this->event as $type => $events){
            foreach ($events as  $event => $func){
                $this->swooleServer->on($event , [$this , $func]);
            }
        }
    }

    /**
     * 设置监听事件
     * @param $type
     * @param $event
     * @return $this
     */
    public function setEvent($type , $event)
    {
        if($type == 'Server'){
            return $this;
        }

        $this->event[$type]=$event;
        return $this;
    }

    /**
     * 事件回调函数
     * @param Swoole\Server $server
     */
    public function onStart(SwooleServer $server)
    {
        $this->pidMap['masterPid']=$server->master_pid;
        $this->pidMap['managerPid']=$server->manager_pid;


//        app('event')->trigger('start',[$server]);
        $this->app->make('event')->trigger('start', [$this,$server]);


    }


    public function onManagerStart(SwooleServer $server){

    }

    public function onManagerStop(SwooleServer $server){

    }


    public function onShutdown(SwooleServer $server){

    }

    public function onWorkerStart(SwooleServer$server, int $workerId){
        $this->pidMap['workerPids']=[
            'id'=>$workerId,
            'pid'=>$server->worker_id,
        ];

        $this->redis = new Redis;
        $this->redis->pconnect("172.19.0.4", 6379);
    }

    public function onWorkerStop(SwooleServer $server, int $workerId){

    }


    public function onWorkerError(SwooleServer $server, int $worker_id, int $worker_pid, int $exit_code, int $signal){

    }




    public function getRedis()
    {
        return $this->redis;
    }



}