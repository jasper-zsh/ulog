<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午6:18
 */

namespace AGarage\ULog;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Storage\StorageInterface;
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

    private $storages = [];

    private $host;
    private $service;

    private static $logger;

    public function __construct($config = []) {
        $config = array_merge_recursive($config, $this->getDefaultConfiguration());
        if (isset($config['storages'])) {
            if (!is_array($config['storages'])) {
                throw new IllegalConfigException($config, '"storages" is not array.');
            }
            foreach ($config['storages'] as $writerConfig) {
                $this->addStorage($this->getStorage($writerConfig));
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
     * @param array $storageConfig
     * @return StorageInterface
     * @throws IllegalConfigException
     */
    private function getStorage(array $storageConfig) {
        if (!isset($storageConfig['class'])) {
            throw new IllegalConfigException($storageConfig, '"class" is not specified.');
        }
        $writer = (new \ReflectionClass($storageConfig['class']))->newInstanceArgs([$storageConfig]);
        if (!($writer instanceof StorageInterface)) {
            throw new IllegalConfigException($storageConfig, '"class" does not implemented StorageInterface.');
        }
        return $writer;
    }

    public function addStorage(StorageInterface $storage) {
        $this->storages[] = $storage;
    }

    /**
     * @return array
     */
    public function getStorages() {
        return $this->storages;
    }

    public function clearStorage() {
        $this->storages = [];
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
        $isException = $log instanceof \Exception;
        $entity = new LogEntity($this->host, $this->service, $tag, $level, null, $this->getCurrentTime());
        if (!$isException) {
            $entity->setContent(trim($log));
        }
        foreach ($this->storages as $storage) {
            /** @var StorageInterface $storage */
            if ($level >= $storage->getLevel()) {
                if ($isException) {
                    $entity->setContent($storage->getFormatter()->stringifyException($log));
                }
                $storage->write($entity);
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