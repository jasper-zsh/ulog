<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:24
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Formatter\DefaultFormatter;
use AGarage\ULog\Formatter\FormatterInterface;

abstract class AbstractStorageWithFormatter implements StorageInterface
{
    protected $formatter;

    public function __construct(array $writerConfig = [])
    {
        $writerConfig = array_merge($this->getDefaultConfiguration(), $writerConfig);
        if (!isset($writerConfig['formatter'])) {
            throw new IllegalConfigException($writerConfig, '"formatter" is not specified.');
        }
        if (!is_array($writerConfig['formatter'])) {
            throw new IllegalConfigException($writerConfig, '"formatter" is not object.');
        }
        if (!isset($writerConfig['formatter']['class'])) {
            throw new IllegalConfigException($writerConfig['formatter'], '"class" is not specified.');
        }
        $formatter = (new \ReflectionClass($writerConfig['formatter']['class']))->newInstanceArgs([$writerConfig['formatter']]);
        if (!($formatter instanceof FormatterInterface)) {
            throw new IllegalConfigException($writerConfig['formatter'], '"class" does not implemented FormatterInterface');
        }
        $this->formatter = $formatter;
    }

    public function getFormatter() {
        return $this->formatter;
    }

    private function getDefaultConfiguration() {
        return [
            'formatter' => [
                'class' => DefaultFormatter::class
            ]
        ];
    }
}