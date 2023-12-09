<?php

require "autoloader.php";
require "functions.php";

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('Medoo', './vendor/Medoo');
$loader->addNamespace('Bramus', './vendor/Bramus');
$loader->addNamespace('Latte', './vendor/Latte');
$loader->addNamespace('Symfony', './vendor/Symfony');

$loader->addNamespace('Panda', './core');


use Panda\Router;
use Symfony\Component\Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env');
}


require "settings.php";

$router = new Router();

$router->run();
