<?php
namespace SwoStar\Event;

use SwoStar\Foundation\Application;

abstract class Listener{

    protected $name='Listener';
    public abstract function handler();

    protected $app;

    public function __construct(Application $app)
    {
        $this->app=$app;
    }

    public function getName()
    {
        return $this->name;
    }

}