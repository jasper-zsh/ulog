<?php

/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 17:55
 */
class ULogReaderTest extends PHPUnit_Framework_TestCase
{
    private $storage;
    /**
     * @var \AGarage\ULog\ULogReader
     */
    private $reader;

    private $path = __DIR__.'/../tmp/test.log';

    public function setUp() {
        $this->storage = new \AGarage\ULog\Storage\SingleFileStorage([
            'path' => $this->path
        ]);
        $this->reader = new \AGarage\ULog\ULogReader();
        $this->reader->setStorage($this->storage);
    }

    public function testTail() {
        $logger = new \AGarage\ULog\ULog();
        $logger->addStorage($this->storage);
        $logger->debug('0');
        $logger->debug('1');
        $logs = $this->reader->tail(2);
        $this->assertEquals('0', $logs[0]->getMessage());
        $this->assertEquals('1', $logs[1]->getMessage());
    }
}