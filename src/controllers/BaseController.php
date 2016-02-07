<?php

namespace unclead\phpcluster\controllers;

use unclead\phpcluster\Application;

/**
 * Class BaseController
 * @package unclead\phpcluster\controllers
 */
abstract class BaseController
{
    /**
     * @var Application
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

}