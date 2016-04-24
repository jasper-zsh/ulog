<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 16-4-20
 * Time: 下午8:44
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\Exception\StorageNotReadyException;
use AGarage\ULog\LogEntity;
use AGarage\ULog\ULog;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class DoctrineStorage implements StorageInterface
{
    use StorageLevelTrait;
    use StorageFormatterTrait;

    /** @var Connection|null  */
    private $conn = null;
    private $tableName;

    private $line = 0;

    public function __construct(array $writerConfig = [])
    {
        $writerConfig = array_merge($this->getDefaultConfiguration(), $writerConfig);
        $this->initLevel($writerConfig);
        $this->initFormatter($writerConfig);
        $this->tableName = $writerConfig['tableName'];
    }

    public function setConnection(Connection $connection) {
        $this->conn = $connection;
    }

    public function write(LogEntity $log)
    {
        if ($this->conn === null) {
            throw new StorageNotReadyException($this, 'Doctrine connection is not specified.');
        }
        $qb = new QueryBuilder($this->conn);
        $qb->insert($this->tableName)
            ->values([
                'host' => $log->getHost(),
                'service' => $log->getService(),
                'tag' => $log->getTag(),
                'level' => $log->getLevel(),
                'content' => $log->getContent(),
                'time' => $log->getTime()
            ])->execute();
    }

    private function getDefaultConfiguration() {
        return [
            'tableName' => 'ulog'
        ];
    }

    /**
     * @param int $line
     */
    public function seek($line)
    {
        $this->line += $line;
    }

    /**
     * @param int $count
     * @return LogEntity
     */
    public function read($count = 1)
    {
        if ($this->conn === null) {
            throw new StorageNotReadyException($this, 'Doctrine connection is not specified.');
        }
        $qb = new QueryBuilder($this->conn);
        $stmt = $qb->select('*')
            ->from($this->tableName, 'log')
            ->setFirstResult($this->line)
            ->setMaxResults($count)
            ->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->line += $count;
        return $this->arrayToEntity($results);
    }

    private function arrayToEntity(array $arr) {
        $logs = [];
        foreach ($arr as &$result) {
            $logs[] = new LogEntity($result['host'], $result['service'], $result['tag'], $result['level'], $result['content'], $result['time']);
        }
    }

    /**
     * @param $line
     */
    public function seekTo($line)
    {
        $this->line = $line;
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
        $stmt = $this->conn->executeQuery('select count(id) from `'.$this->tableName.'`');
        return intval($stmt->fetch(\PDO::FETCH_BOUND)[0]);
    }
}