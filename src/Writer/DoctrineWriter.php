<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:44
 */

namespace AGarage\ULog\Writer;


use AGarage\ULog\Exception\WriterNotReadyException;
use AGarage\ULog\ULog;
use Doctrine\DBAL\Driver\Connection;

class DoctrineWriter implements WriterInterface
{
    /** @var Connection|null  */
    private $conn = null;
    private $level;
    private $tableName;

    public function __construct(array $writerConfig = [])
    {
        $writerConfig = array_merge($this->getDefaultConfiguration(), $writerConfig);
        $this->level = $writerConfig['level'];
        $this->tableName = $writerConfig['tableName'];
    }

    public function setConnection(Connection $connection) {
        $this->conn = $connection;
    }

    public function write($host, $service, $tag, $level, $content, $time)
    {
        if ($this->conn === null) {
            throw new WriterNotReadyException($this, 'Doctrine connection is not specified.');
        }
        $stmt = $this->conn->prepare("insert into `$this->tableName` (`host`, `service`, `tag`, `level`, `content`, `time`) VALUES (:host, :service, :tag, :level, :content, :time)");
        $stmt->bindValue('host', $host);
        $stmt->bindValue('service', $service);
        $stmt->bindValue('tag', $tag);
        $stmt->bindValue('level', $level);
        $stmt->bindValue('content', $content);
        $stmt->bindValue('time', $time);
        $stmt->execute();
    }

    public function getLevel()
    {
        return $this->level;
    }

    private function getDefaultConfiguration() {
        return [
            'level' => ULog::DEBUG,
            'tableName' => 'ulog'
        ];
    }
}