<?php

namespace PhpCluster;

/**
 * Class Cmd
 * @package PhpCluster
 */
class Cmd {

    /**
     * @var int
     */
    private $count = 0;

    /**
     * Returns current value of counter.
     *
     * @return int
     */
    public function getCount ()
    {
        return $this->count;
    }

    /**
     * Set current value of counter.
     *
     * @param $v
     */
    public function setCount($v)
    {
        $this->count = $v;
    }

    /**
     * Performs increment operation and returns the result.
     *
     * @return int
     */
    public function increment ()
    {
        return ++$this->count;
    }
}