<?php

declare(strict_types=1);

session_start();

require "settings.php";

require "functions.php";

require "autoloader.php";

$loader = new Psr4Autoloader();
$loader->register();

$loader->addNamespace('Psr', __DIR__ . "/vendor/Psr");

$loader->addNamespace('MongoDB', __DIR__ . '/vendor/MongoDB');
require __DIR__ . "/vendor/MongoDB/functions.php";

$loader->addNamespace('Symfony', __DIR__ . '/vendor/Symfony');

$loader->addNamespace('Bramus', __DIR__ . '/vendor/Bramus');
$loader->addNamespace('Latte', __DIR__ . '/vendor/Latte');

$loader->addNamespace('Panda', __DIR__ . '/core');


// load dotenv and if no env, go to installation 
use Symfony\Component\Dotenv\Dotenv;

$dotenv_file = __DIR__ . '/.env';

if (!file_exists($dotenv_file)) {
    touch($dotenv_file);
}

$dotenv = new Dotenv();
$dotenv->load($dotenv_file);

if (!env("SITE_READY")) {
    ob_start();
    header("Location: /install.php");
    ob_end_flush();
    exit();
}

// load theme's controllers
$current_theme = env('CURRENT_THEME');
$theme_info = get_theme_info($current_theme);

require $theme_info['current_theme_dir'] . "/settings.php";
$loader->addNamespace('Panda', $theme_info['current_theme_dir']);


// main panda cms logic here

use Panda\Panda;

global $pandadb;
global $logger;
global $router;

$panda = Panda::getInstance();

$pandadb = $panda->db;
$logger = $panda->logger;
$router = $panda->router;

$panda->start();
