<?php

namespace PhpCluster\Command;

use PhpCluster\Cmd;

/**
 * Class UpdateCommand
 * @package PhpCluster\Command
 */
class UpdateCountCommand implements Command
{
    /**
     * @var Cmd
     */
    private $entity;

    /**
     * @var int
     */
    private $count;

    public function __construct($entity, $count)
    {
        $this->entity = $entity;
        $this->count = $count;
    }

    public function execute()
    {
        $currentCount = $this->entity->getCount();
        if ($currentCount < $this->count) {
            $this->entity->setCount($this->count);
        }
    }

}