<?php
namespace common\helpers;


class StringHelper
{
    /**
     * @param string $string
     * @param int $num
     * @return string
     */
    public static function truncateWords($string, int $num=15): string
    {
        if(!$string){
            return '';
        }
        $workString = mb_substr($string, 0, $num * 15);
        $wordsArr = explode(' ', $workString);
        $slicedArr = array_slice($wordsArr, 0, $num);
        $string = trim(implode(' ', $slicedArr));
        if (count($wordsArr) > count($slicedArr)) $string .= '...';
        return $string;
    }
}