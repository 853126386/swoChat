<?php
namespace SwoStar\Config;

class Config{

    protected $items = [];

    public function __construct()
    {
        $configPath = app()->getBasePath().'/config';

        $this->items=$this->phpParser($configPath);
    }

    /**
     * 读取PHP文件类型的配置文件
     * @return [type] [description]
     */
    protected function phpParser($configPath)
    {
        // 1. 找到文件
        // 此处跳过多级的情况
        $files = scandir($configPath);
        $data = null;
        // 2. 读取文件信息
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if(is_dir($configPath.'/'.$file)){
                $data[$file]=$this->phpParser($configPath.'/'.$file);
            }else{
                // 2.1 获取文件名
                $filename = \stristr($file, ".php", true);
                // 2.2 读取文件信息
                $data[$filename] = include $configPath."/".$file;
            }

        }

        // 3. 返回
        return $data;
    }

    /**
     * @return array
     */
    public function get($keyStr)
    {
        $arr=explode('.',$keyStr);
        $config=$this->items;
        foreach ($arr as $key){
            $config=$config[$key] ?? [];
        }
        return $config;
    }




}