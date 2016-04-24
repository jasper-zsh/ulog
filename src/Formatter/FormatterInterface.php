<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:15
 */

namespace AGarage\ULog\Formatter;


use AGarage\ULog\LogEntity;

interface FormatterInterface
{
    public function __construct(array $formatterConfig);

    /**
     * @param LogEntity $log
     * @return string
     */
    public function format(LogEntity $log);

    /**
     * @param $str
     * @return LogEntity
     */
    public function deformat($str);

    public function stringifyException(\Exception $ex);
}