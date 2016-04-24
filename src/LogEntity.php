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
    protected $tag;
    protected $level;
    protected $content;
    protected $time;

    public function __construct($host = null, $service = null, $tag = null, $level = null, $content = null, $time = null)
    {
        $this->host = $host;
        $this->service = $service;
        $this->tag = $tag;
        $this->level = $level;
        $this->content = $content;
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

    public function getTag() {
        return $this->tag;
    }

    public function setTag($tag) {
        $this->tag = $tag;
        return $this;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }
}