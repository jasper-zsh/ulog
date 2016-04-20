<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:15
 */

namespace AGarage\ULog\Formatter;


use AGarage\ULog\ULog;

class DefaultFormatter implements FormatterInterface
{

    public function format($host, $service, $tag, $level, $content, $time)
    {
        switch ($level) {
            case ULog::DEBUG:
                $level = 'DEBUG';
                break;
            case ULog::INFO:
                $level = 'INFO';
                break;
            case ULog::WARN:
                $level = 'WARN';
                break;
            case ULog::ERROR:
                $level = 'ERROR';
                break;
            case ULog::FATAL:
                $level = 'FATAL';
                break;
        }
        $milli = $time % 1000;
        $time = date('Y-m-d H:m:s', $time / 1000);
        return "$time.$milli - $level - $service @ $host - $tag - $content";
    }

    public function __construct(array $formatterConfig)
    {

    }
}