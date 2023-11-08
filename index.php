<?php

require "autoloader.php";
require "functions.php";

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('Bramus', './vendor/Bramus');
$loader->addNamespace('Latte', './vendor/Latte');
$loader->addNamespace('Panda', './core');

use Panda\Router;

$router = new Router();


$router->run();
