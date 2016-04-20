<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午6:42
 */

namespace AGarage\ULog\Exception;


class IllegalConfigException extends \Exception
{
    public function __construct($config, $msg)
    {
        $this->message = "In config: \n".print_r($config, true)."Msg: $msg";
    }
}