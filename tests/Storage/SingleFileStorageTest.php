<?php

/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 3:03
 */
class SingleFileStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \AGarage\ULog\Storage\SingleFileStorage
     */
    private $storage;

    private $path =  __DIR__.'/../../tmp/test.log';

    private $passes = 10;
    
    public function setUp() {
//        if (file_exists($this->path)) {
//            unlink($this->path);
//        }
        $this->storage = new \AGarage\ULog\Storage\SingleFileStorage([
            'path' => $this->path
        ]);
    }

    /**
     * @dataProvider logEntityProvider
     */
    public function testWrite(\AGarage\ULog\LogEntity $log) {
        $this->storage->write($log);
        $fileContent = file_get_contents($this->path);
        $lines = explode("\n", $fileContent);
        $line = $lines[count($lines) - 2];
        $this->assertEquals($this->storage->getFormatter()->format($log), $line);
    }

    /**
     * @depends testWrite
     */
    public function testReadOne() {
        $logs = $this->storage->read();
        $this->assertEquals('0', $logs[0]->getContent());
        $this->assertEquals(1, $this->storage->getCurrentLine());
    }

    /**
     * @depends testWrite
     */
    public function testSeekTo() {
        $this->storage->seekTo(3);
        $logs = $this->storage->read();
        $this->assertEquals('3', $logs[0]->getContent());
        $this->assertEquals(4, $this->storage->getCurrentLine());
    }

    public function logEntityProvider() {
        $datas = [];
        for ($i = 0; $i < $this->passes; $i ++) {
            $datas[] = [$this->buildLogEntity(\AGarage\ULog\ULog::DEBUG, $i)];
        }
        return $datas;
    }

    private function buildLogEntity($level, $content) {
        return new \AGarage\ULog\LogEntity('localhost', 'ULog Unit Test', 'SingleFileStorageTest', $level, $content, time() * 1000);
    }
}