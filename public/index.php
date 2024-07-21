<?php

declare(strict_types=1);

require __DIR__ . "/../settings.php";

require __DIR__ . "/../functions.php";

require __DIR__ . "/../autoloader.php";

$loader = new Psr4Autoloader();
$loader->register();

$loader->addNamespace('Psr', "../vendor/Psr");

$loader->addNamespace('MongoDB', '../vendor/MongoDB');
require __DIR__ . "/../vendor/MongoDB/functions.php";

$loader->addNamespace('Symfony', '../vendor/Symfony');

$loader->addNamespace('Bramus', '../vendor/Bramus');
$loader->addNamespace('Latte', '../vendor/Latte');

$loader->addNamespace('Panda', '../core');


// load dotenv and if no env, go to installation 
use Symfony\Component\Dotenv\Dotenv;

$dotenv_file = PANDA_ROOT . '/.env';

if (file_exists($dotenv_file)) {
    $dotenv = new Dotenv();
    $dotenv->load($dotenv_file);
}

if (!env("SITE_READY")) {
    ob_start();
    header("Location: /install.php");
    ob_end_flush();
    exit();
}


// main panda cms logic here

use Panda\Panda;

global $pandadb;
global $logger;

$panda = Panda::getInstance();

$pandadb = $panda->db;
$logger = $panda->logger;

$panda->start();
