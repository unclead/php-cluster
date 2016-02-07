<?php

namespace unclead\phpcluster\commands;

use unclead\phpcluster\models\Cmd;

/**
 * Class UpdateCommand
 * @package unclead\phpcluster\commands
 */
class UpdateCommand extends BaseCommand
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