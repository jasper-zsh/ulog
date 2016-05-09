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
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ULog extends AbstractLogger
{
    private $storages = [];

    private $host;
    private $service;

    private static $logger;

    private static $levels = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7
    ];

    public function __construct($config = []) {
        $config = array_merge($config, $this->getDefaultConfiguration());
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

    public function log($level, $message, array $context = []) {
        $isException = $message instanceof \Exception;
        $entity = new LogEntity($this->host, $this->service, $level, null, $this->getCurrentTime());
        if (!$isException) {
            $entity->setMessage(trim($message));
        }
        foreach ($this->storages as $storage) {
            /** @var StorageInterface $storage */
            if (self::$levels[$level] >= self::$levels[$storage->getLevel()]) {
                if ($isException) {
                    $entity->setMessage($storage->getFormatter()->stringifyException($message));
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