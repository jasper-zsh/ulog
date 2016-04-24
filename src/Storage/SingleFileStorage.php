<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:13
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Formatter\DefaultFormatter;
use AGarage\ULog\LogEntity;
use AGarage\ULog\ULog;

class SingleFileStorage implements StorageInterface
{
    use StorageLevelTrait;
    use StorageFormatterTrait;

    private $path;

    private $file = null;
    private $line = 0;

    const BLOCK_SIZE = 1024;

    public function __construct(array $writerConfig = [])
    {
        $this->initLevel($writerConfig);
        $this->initFormatter($writerConfig);
        if (!isset($writerConfig['path'])) {
            throw new IllegalConfigException($writerConfig, '"path" is not specified.');
        }
        $this->path = $writerConfig['path'];
        $this->prepareDir(dirname($this->path));
    }

    private function prepareDir($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function write(LogEntity $log)
    {
        $log = $this->getFormatter()->format($log)."\n";
        file_put_contents($this->path, $log, FILE_APPEND);
    }

    /**
     * @param int $line
     */
    public function seek($line)
    {
        if ($this->file === null) {
            $this->openFile();
        }
        $target = $this->line + $line;
        $pos = 0;
        $size = 0;
        while ($this->line < $target) {
            $pos = 0;
            $str = fread($this->file, self::BLOCK_SIZE);
            $size = strlen($str);
            for (; $this->line < $target && $pos < $size; $pos ++) {
                if ($str[$pos] == "\n") {
                    $this->line ++;
                }
            }
        }
        fseek($this->file, -($size - $pos), SEEK_CUR);
    }

    public function seekTo($line)
    {
        if ($this->file === null) {
            $this->openFile();
        }
        fseek($this->file, 0, SEEK_SET);
        $this->seek($line);
    }

    /**
     * @param int $count
     * @return array
     */
    public function read($count = 1)
    {
        if ($this->file === null) {
            $this->openFile();
        }
        $logs = [];
        while (count($logs) < $count) {
            $log = $this->readLine();
            if ($log !== null) {
                $logs[] = $log;
            } else {
                break;
            }
        }
        return $logs;
    }

    private function readLine() {
        $str = '';
        while (!feof($this->file)) {
            $c = fread($this->file, 1);
            if ($c === false) {
                break;
            }
            if ($c !== "\n") {
                $str .= $c;
            } else {
                break;
            }
        }
        if (strlen($str) === 0) {
            return null;
        }
        $this->line ++;
        return $this->getFormatter()->deformat(trim($str));
    }

    private function openFile() {
        $this->file = fopen($this->path, 'r');
        $this->line = 0;
    }

    /**
     * @return int
     */
    public function getCurrentLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function count()
    {
        $fileContent = file_get_contents($this->path);
        return substr_count($fileContent, "\n");
    }
}