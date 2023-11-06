<?php

require "autoloader.php";

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('Panda', './core');

use Panda\Router;

$router = new Router();


$router->run();
