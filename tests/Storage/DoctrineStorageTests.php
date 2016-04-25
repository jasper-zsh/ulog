<?php

/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-25
 * Time: 下午5:53
 */
class DoctrineStorageTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var \AGarage\ULog\Storage\DoctrineStorage
     */
    private $storage;
    /**
     * @var \AGarage\ULog\Formatter\FormatterInterface
     */
    private $formatter;

    public function setUp() {
        $this->storage = new \AGarage\ULog\Storage\DoctrineStorage();
        $conn = \Doctrine\DBAL\DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'root',
            'password' => 'ta992080fe',
            'dbname' => 'ibigstor',
            'charset' => 'utf8'
        ]);
        $this->storage->setConnection($conn);
    }

    /**
     * @param \AGarage\ULog\LogEntity $log
     * @dataProvider logsDataProvider
     */
    public function testWrite(\AGarage\ULog\LogEntity $log) {
        $this->storage->write($log);
    }

    public function logsDataProvider() {
        $datas = [];
        $datas[] = [$this->buildLogEntity('test log 1')];
        $datas[] = [$this->buildLogEntity(new Exception())];
        return $datas;
    }

    private function buildLogEntity($content) {
        if ($this->formatter == null) {
            $this->formatter = new \AGarage\ULog\Formatter\DefaultFormatter([]);
        }
        $log = new \AGarage\ULog\LogEntity('localhost', 'ULog Test', 'DoctrineStorage', \AGarage\ULog\ULog::DEBUG, null, time() * 1000);
        if ($content instanceof Exception) {
            $content = $this->formatter->stringifyException($content);
        }
        $log->setContent($content);
        return $log;
    }
}