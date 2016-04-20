<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午6:18
 */

namespace AGarage\ULog\Writer;


interface WriterInterface
{
    public function __construct(array $writerConfig = []);
    public function write($host, $service, $tag, $level, $content, $time);
    public function getLevel();
}