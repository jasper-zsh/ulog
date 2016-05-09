<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 17:48
 */

namespace AGarage\ULog;


use AGarage\ULog\Storage\StorageInterface;

class ULogReader
{
    /**
     * @var StorageInterface
     */
    private $storage;

    public function setStorage(StorageInterface $storage) {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @param int $count
     * @return array
     */
    public function tail($count = 1) {
        $total = $this->storage->count();
        $start = $total - $count;
        if ($start < 0) {
            $start = 0;
        }
        $this->storage->seekTo($start);
        $logs = $this->storage->read($count);
        return $logs;
    }
}