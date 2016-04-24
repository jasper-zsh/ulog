<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:48
 */

namespace AGarage\ULog\Exception;


use AGarage\ULog\Storage\StorageInterface;

class StorageNotReadyException extends \Exception
{
    public function __construct(StorageInterface $writer, $message)
    {
        $writerName = get_class($writer);
        $this->message = "$writerName: $message";
    }
}