<?php

require "autoloader.php";

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('Bramus', './vendor/Bramus');
$loader->addNamespace('Panda', './core');

use Panda\Router;

$router = new Router();


$router->run();
