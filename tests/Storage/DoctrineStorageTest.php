<?php

/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-25
 * Time: 下午5:53
 */
class DoctrineStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \AGarage\ULog\Storage\DoctrineStorage
     */
    private $storage;
    /**
     * @var \AGarage\ULog\Formatter\FormatterInterface
     */
    private $formatter;
    private $passes = 10;

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
     * @dataProvider logEntityProvider
     */
    public function testWrite(\AGarage\ULog\LogEntity $log) {
        $this->storage->write($log);
    }

    /**
     * @depends testWrite
     */
    public function testReadOne() {
        $logs = $this->storage->read();
        $this->assertEquals('0', $logs[0]->getMessage());
        $this->assertEquals(1, $this->storage->getCurrentLine());
    }

    /**
     * @depends testWrite
     */
    public function testSeekTo() {
        $this->storage->seekTo(3);
        $logs = $this->storage->read();
        $this->assertEquals('3', $logs[0]->getMessage());
        $this->assertEquals(4, $this->storage->getCurrentLine());
    }

    public function testTail() {
        $reader = new \AGarage\ULog\ULogReader();
        $reader->setStorage($this->storage);
        $logger = new \AGarage\ULog\ULog();
        $logger->addStorage($this->storage);
        $logger->debug('haha');
        $logger->debug('hehe');
        $logs = $reader->tail(2);
        $this->assertEquals('haha', $logs[0]->getMessage());
        $this->assertEquals('hehe', $logs[1]->getMessage());
    }

    public function logEntityProvider() {
        $datas = [];
        for ($i = 0; $i < $this->passes; $i ++) {
            $datas[] = [$this->buildLogEntity(\Psr\Log\LogLevel::DEBUG, $i)];
        }
        return $datas;
    }

    private function buildLogEntity($level, $content) {
        return new \AGarage\ULog\LogEntity('localhost', 'ULog Unit Test', $level, $content, time() * 1000);
    }
}