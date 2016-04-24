<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 17:11
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Formatter\DefaultFormatter;
use AGarage\ULog\Formatter\FormatterInterface;

trait StorageFormatterTrait
{
    private $formatter;
    
    private function initFormatter($config) {
        $config = array_merge($this->getDefaultFormatterConfiguration(), $config);
        if (!isset($config['formatter'])) {
            throw new IllegalConfigException($config, '"formatter" is not specified.');
        }
        if (!is_array($config['formatter'])) {
            throw new IllegalConfigException($config, '"formatter" is not object.');
        }
        if (!isset($config['formatter']['class'])) {
            throw new IllegalConfigException($config['formatter'], '"class" is not specified.');
        }
        $formatter = (new \ReflectionClass($config['formatter']['class']))->newInstanceArgs([$config['formatter']]);
        if (!($formatter instanceof FormatterInterface)) {
            throw new IllegalConfigException($config['formatter'], '"class" does not implemented FormatterInterface');
        }
        $this->formatter = $formatter;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter() {
        return $this->formatter;
    }

    public function setFormatter(FormatterInterface $formatter) {
        $this->formatter = $formatter;
        return $this;
    }

    private function getDefaultFormatterConfiguration() {
        return [
            'formatter' => [
                'class' => DefaultFormatter::class
            ]
        ];
    }
}