<?php

declare(strict_types=1);

require "functions.php";

require "autoloader.php";
$loader = new Psr4Autoloader();
$loader->register();

$loader->addNamespace('Psr', "./vendor/Psr");

$loader->addNamespace('MongoDB', './vendor/MongoDB');
require "./vendor/MongoDB/functions.php";

$loader->addNamespace('Symfony', './vendor/Symfony');
$loader->addNamespace('Medoo', './vendor/Medoo');
$loader->addNamespace('Bramus', './vendor/Bramus');
$loader->addNamespace('Latte', './vendor/Latte');

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
