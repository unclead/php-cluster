<?php

namespace PhpCluster\Command;

use PhpCluster\Cmd;

/**
 * Class IncrementCommand
 * @package PhpCluster\Command
 */
class IncrementCountCommand implements Command
{
    /**
     * @var Cmd
     */
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return int
     */
    public function execute()
    {
        return $this->entity->increment();
    }
}