<?php
use SwoStar\Console\Input;

use SwoStar\Foundation\Application;

if (!function_exists('app')) {
    /**
     * 六星教育 @shineyork老师
     * @param  [type] $a [description]
     * @return Application
     */
    function app($a = null)
    {
        if (empty($a)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($a);
    }
}
if (!function_exists('dd')) {
    /**
     * 六星教育 @shineyork老师
     * @param  [type] $a [description]
     * @return Application
     */
    function dd($message, $description = null)
    {
        Input::info($message, $description);
    }
}


if (!function_exists('debug_log')) {
    /**
     * 记录日志
     * @param content $msg 打印内容
     * @param string $pre 日志类型
     */
    function debug_log($msg, $pre = "debug"){
        $root = app()->getBasePath() . '/storage/';
        $path = $root.'logs/debug_log';
        if (!is_dir($path)) mkdir($path, 0777, true);

        $filename = $path.'/' . $pre."_".date("Y-m-d").'.log';

        if (is_array($msg) || is_object($msg))
            $msg = print_r($msg, true);

        $msg = "[".date("Y-m-d H:i:s")."]".$msg."\n";
        file_put_contents($filename,$msg,FILE_APPEND);
    }
}
