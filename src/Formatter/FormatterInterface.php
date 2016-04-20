<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:15
 */

namespace AGarage\ULog\Formatter;


interface FormatterInterface
{
    public function __construct(array $formatterConfig);

    public function format($host, $service, $tag, $level, $content, $time);
}