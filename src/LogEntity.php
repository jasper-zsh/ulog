<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 2016/4/24
 * Time: 1:42
 */

namespace AGarage\ULog;


class LogEntity
{
    protected $host;
    protected $service;
    protected $level;
    protected $message;
    protected $time;

    public function __construct($host = null, $service = null, $level = null, $message = null, $time = null)
    {
        $this->host = $host;
        $this->service = $service;
        $this->level = $level;
        $this->message = $message;
        $this->time = $time;
    }

    public function getHost() {
        return $this->host;
    }

    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    public function getService() {
        return $this->service;
    }

    public function setService($service) {
        $this->service = $service;
        return $this;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }
}