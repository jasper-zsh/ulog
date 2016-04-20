<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:13
 */

namespace AGarage\ULog\Writer;


use AGarage\ULog\Exception\IllegalConfigException;
use AGarage\ULog\Formatter\DefaultFormatter;
use AGarage\ULog\ULog;

class SingleFileWriter extends AbstractWriterWithFormatter
{
    private $path;
    private $level;

    public function __construct(array $writerConfig = [])
    {
        parent::__construct($writerConfig);
        $writerConfig = array_merge($this->getDefaultConfiguration(), $writerConfig);
        if (!isset($writerConfig['path'])) {
            throw new IllegalConfigException($writerConfig, '"path" is not specified.');
        }
        $this->path = $writerConfig['path'];
        $this->level = $writerConfig['level'];
    }

    public function write($host, $service, $tag, $level, $content, $time)
    {
        $log = $this->formatter->format($host, $service, $tag, $level, $content, $time)."\n";
        file_put_contents($this->path, $log, FILE_APPEND);
    }

    public function getLevel()
    {
        return $this->level;
    }

    protected function getDefaultConfiguration() {
        return [
            'level' => ULog::DEBUG
        ];
    }
}