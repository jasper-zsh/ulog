<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 4:12
 */

namespace AGarage\ULog\Storage;


use AGarage\ULog\ULog;

trait StorageLevelTrait
{
    private $level;

    private function initLevel($config) {
        $config = array_merge($this->getDefaultLevelConfiguration(), $config);
        $this->level = $config['level'];
    }

    public function getLevel() {
        return $this->level;
    }

    private function getDefaultLevelConfiguration() {
        return [
            'level' => ULog::DEBUG
        ];
    }
}