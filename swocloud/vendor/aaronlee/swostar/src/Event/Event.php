<?php
namespace SwoStar\Event;

use SwoStar\Foundation\Application;

class Event{

    protected $events=[];


    /**
     * 注册事件
     * @param $event
     * @param $callback
     * @return $this
     */
    public function register($event,$callback)
    {
        if(isset($this->events[$event])){
            unset($this->events[$event]);
        }
        $this->events[$event]=['callback'=>$callback];

        return $this;
    }

    /**
     * 触发事件
     * @param $event
     * @param null $param
     * @return false|mixed
     */
    public function trigger($event,$param=null)
    {
        if(!isset($this->events[$event])){
            return false;
        }
        return call_user_func($this->events[$event]['callback'],...$param);
    }

    public function getEvents($event = null)
    {
        return empty($event) ? $this->events : $this->events[$event];
    }

}