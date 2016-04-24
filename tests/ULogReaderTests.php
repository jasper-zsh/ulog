<?php

/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 17:55
 */
class ULogReaderTests extends PHPUnit_Framework_TestCase
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
        $logs = $this->reader->tail(2);
        var_dump($logs);
        $logs = $this->reader->tail(3);
        var_dump($logs);
    }
}