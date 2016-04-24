<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午6:18
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\Formatter\FormatterInterface;
use AGarage\ULog\LogEntity;

interface StorageInterface
{
    public function __construct(array $storageConfig = []);

    /**
     * @return FormatterInterface
     */
    public function getFormatter();

    public function setFormatter(FormatterInterface $formatter);

    /**
     * @param LogEntity $log
     */
    public function write(LogEntity $log);

    /**
     * @param int $line
     */
    public function seek($line);

    /**
     * @param $line
     */
    public function seekTo($line);

    /**
     * @param int $count
     * @return array
     */
    public function read($count = 1);

    /**
     * @return int
     */
    public function count();

    /**
     * @return int
     */
    public function getCurrentLine();
    public function getLevel();
}