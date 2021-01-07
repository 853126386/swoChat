<?php


if (!function_exists('dd')) {

    /**
     * 打印
     * @param $message
     * @param null $description
     */
  function dd($message, $description = null)
    {
        $return = "======>>> " . $description . " start\n";
        if (\is_array($message)) {
            $return = $return . \var_export($message, true);
        } else {
            $return .= $message . "\n";
        }
        $return .= "======>>> " . $description . " end\n";
        echo $return;
    }
}
if (!function_exists('config')) {
    /**
     * huoqu
     * @param $key
     */
    function config($key){
       $config=include  __DIR__."/../Config/swocloud.php";
        if($key){
           $keyArray=explode('.',$key);
           foreach ($keyArray as $key=>$value){
                   if(isset($config[$value])){
                        if($key==(count($keyArray)-1)){
                            return $config[$value];
                        }
                       $config=$config[$value];
                   }
           }
       }
       return false;
    }
}

