<?php

namespace unclead\phpcluster\models;

/**
 * Class Cmd
 * @package unclead\phpcluster\models
 */
class Cmd {

    /**
     * @var int
     */
    private $count = 0;

    public function getCount ()
    {
        return $this->count;
    }

    public function setCount($v)
    {
        $this->count = $v;
    }

    public function increment ()
    {
        return ++$this->count;
    }
}