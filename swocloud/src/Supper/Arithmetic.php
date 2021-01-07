<?php
namespace SwoCloud\Supper;

class Arithmetic
{
    protected static $roundLastIndex = 0;

    /**轮训算法
     * @param array $list
     * @return mixed
     */
    public static function round(array $list)
    {
        $index = self::$roundLastIndex;
        $url = $list[$index];
        if ($index + 1 > count($list) - 1) {
            self::$roundLastIndex = 0;
        } else {
            self::$roundLastIndex++;
        }
        return  $url;
    }

}
