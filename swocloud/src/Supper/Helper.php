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


