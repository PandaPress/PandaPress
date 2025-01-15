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

$current_theme_id = (new \Panda\Models\ThemeSettings())->getCurrentThemeId();
$current_theme_dir = PANDA_THEMES . "/$current_theme_id";

$loader->addNamespace('Panda\Theme', $current_theme_dir);
require $current_theme_dir . "/routes.php";
require $current_theme_dir . "/settings.php";


// main panda press logic here

use Panda\Panda;

global $pandadb;
global $logger;
global $router;

$panda = Panda::getInstance();

$pandadb = $panda->db;
$logger = $panda->logger;
$router = $panda->router;

$panda->start();
