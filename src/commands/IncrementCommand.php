<?php

namespace unclead\phpcluster\commands;

use unclead\phpcluster\models\Cmd;

/**
 * Class IncrementCommand
 * @package unclead\phpcluster\commands
 */
class IncrementCommand extends BaseCommand
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