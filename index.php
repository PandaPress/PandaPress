<?php

require "autoloader.php";

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('PandaCMS', './core');

use PandaCMS\Bramus\Router;


$router = new Router();
$router->setNamespace("\PandaCMS\Controller");

$router->get('/', 'HomeController@index');

$router->get('/about', function () {
    echo 'About Page Contents';
});

$router->run();
