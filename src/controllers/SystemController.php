<?php

namespace unclead\phpcluster\controllers;

class SystemController extends BaseController
{
    public function getHealthCheck()
    {
        return 1;
    }
}