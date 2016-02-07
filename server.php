<?php

require __DIR__ . '/vendor/autoload.php';

use PhpCluster\Application;

$app = new Application($argv);
$app->run();






