<?php

declare(strict_types=1);

require "../settings.php";

require "../functions.php";

require "../autoloader.php";


\Panda\Session::start('Strict');


// load dotenv and if no env, go to installation 
use Symfony\Component\Dotenv\Dotenv;

$dotenv_file = PANDA_ROOT . '/.env';

if (!file_exists($dotenv_file)) {
    touch($dotenv_file);
}

$dotenv = new Dotenv();
$dotenv->load($dotenv_file);

if (!env("SITE_READY", true)) {
    ob_start();
    header("Location: /install.php");
    ob_end_flush();
    exit();
}

// main panda press logic here

use Panda\Core as Panda;

global $router;
global $pandadb;
global $logger;


$panda = Panda::getInstance();
$pandadb = $panda->db;
$logger = $panda->logger;

$router = $panda->router;

$router->setThemeRoutes();

$panda->start();
