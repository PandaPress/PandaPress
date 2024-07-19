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

$loader->addNamespace('Bramus', './vendor/Bramus');
$loader->addNamespace('Latte', './vendor/Latte');

$loader->addNamespace('Panda', './core');


use Symfony\Component\Dotenv\Dotenv;
use Panda\Panda;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env');
}

if (getenv("SITE_READY")) {
    global $pandadb;
    global $logger;

    $panda = Panda::init();

    $pandadb = $panda->db;
    $logger = $panda->logger;

    $panda->start();
} else {
    ob_start();
    header("Location: /install.php");
    ob_end_flush();
}
