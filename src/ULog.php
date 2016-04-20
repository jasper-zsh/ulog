<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午6:18
 */

namespace AGarage\ULog;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Writer\WriterInterface;

class ULog
{
    const ALL = 0;
    const OFF = 100;
    const DEBUG = 1;
    const INFO = 11;
    const WARN = 21;
    const ERROR = 31;
    const FATAL = 41;

    private $writers = [];

    private $host;
    private $service;

    private static $logger;

    public function __construct($config = []) {
        $config = array_merge_recursive($config, $this->getDefaultConfiguration());
        if (isset($config['writers'])) {
            if (!is_array($config['writers'])) {
                throw new IllegalConfigException($config, '"writers" is not array.');
            }
            foreach ($config['writers'] as $writerConfig) {
                $this->addWriter($this->getWriter($writerConfig));
            }
        }
        $this->host = $config['host'];
        $this->service = $config['service'];
    }

    public static function initialize($config = []) {
        self::$logger = new ULog($config);
    }

    /**
     * @return ULog
     */
    public static function getLogger() {
        return self::$logger;
    }

    /**
     * @param array $writerConfig
     * @return WriterInterface
     * @throws IllegalConfigException
     */
    private function getWriter(array $writerConfig) {
        if (!isset($writerConfig['class'])) {
            throw new IllegalConfigException($writerConfig, '"class" is not specified.');
        }
        $writer = (new \ReflectionClass($writerConfig['class']))->newInstanceArgs([$writerConfig]);
        if (!($writer instanceof WriterInterface)) {
            throw new IllegalConfigException($writerConfig, '"class" does not implemented WriterInterface.');
        }
        return $writer;
    }

    public function addWriter(WriterInterface $writer) {
        $this->writers[] = $writer;
    }

    public function clearWriter() {
        $this->writers = [];
    }

    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    public function setService($service) {
        $this->service = $service;
        return $this;
    }

    public function debug($tag, $log) {
        $this->log(self::DEBUG, $tag, $log);
    }

    public function info($tag, $log) {
        $this->log(self::INFO, $tag, $log);
    }

    public function warn($tag, $log) {
        $this->log(self::WARN, $tag, $log);
    }

    public function error($tag, $log) {
        $this->log(self::ERROR, $tag, $log);
    }

    public function fatal($tag, $log) {
        $this->log(self::FATAL, $tag, $log);
    }

    public function log($level, $tag, $log) {
        foreach ($this->writers as $writer) {
            /** @var WriterInterface $writer */
            if ($level >= $writer->getLevel()) {
                $writer->write($this->host, $this->service, $tag, $level, trim($log), $this->getCurrentTime());
            }
        }
    }

    protected function getCurrentTime() {
        list($usec, $sec) = explode(' ', microtime());
        return (int)($sec * 1000 + $usec * 1000);
    }

    protected function getDefaultConfiguration() {
        return [
            'host' => 'localhost',
            'service' => 'ULog'
        ];
    }
}