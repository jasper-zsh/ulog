<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:15
 */

namespace AGarage\ULog\Formatter;


use AGarage\ULog\LogEntity;
use AGarage\ULog\ULog;

class DefaultFormatter implements FormatterInterface
{

    public function format(LogEntity $log)
    {
        $level = strtoupper($log->getLevel());
        $milli = $log->getTime() % 1000;
        $time = date('Y-m-d H:m:s', $log->getTime() / 1000);
        $content = str_replace("\n", '{\n}', $log->getMessage());
        return "$time.$milli - $level - {$log->getService()} @ {$log->getHost()} - $content";
    }

    public function __construct(array $formatterConfig)
    {

    }

    /**
     * @param $str
     * @return LogEntity
     */
    public function deformat($str)
    {
        $log = new LogEntity();
        $pos = strpos($str, '.');
        $timeStr = substr($str, 0, $pos);
        $time = strtotime($timeStr);
        $str = substr($str, $pos + 1);

        $pos = strpos($str, ' - ');
        $milliStr = substr($str, 0, $pos);
        $time = $time * 1000 + intval($milliStr);
        $log->setTime($time);
        $str = substr($str, $pos + 3);

        $pos = strpos($str, ' - ');
        $levelStr = substr($str, 0, $pos);
        $log->setLevel(strtolower($levelStr));
        $str = substr($str, $pos + 3);

        $pos = strpos($str, ' @ ');
        $service = substr($str, 0, $pos);
        $log->setService($service);
        $str = substr($str, $pos + 3);

        $pos = strpos($str, ' - ');
        $host = substr($str, 0, $pos);
        $log->setHost($host);
        $str = substr($str, $pos + 3);

        $content = str_replace('{\n}', "\n", $str);
        $log->setMessage($content);

        return $log;
    }

    public function stringifyException(\Exception $ex)
    {
        $str = "Exception: ".get_class($ex)."\nCode: {$ex->getCode()}\nFile: {$ex->getFile()}\nLine: {$ex->getLine()}\nMessage: {$ex->getMessage()}\nTrace: {$ex->getTraceAsString()}";
        return $str;
    }
}