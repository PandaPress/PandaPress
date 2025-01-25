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

// ping mongodb server
try {
    $uri = env('MONGO_URI');

    $options = env('APP_ENV') === 'production'
        ?   [
            'tls' => true,
            'tlsCAFile' => PANDA_ROOT . env("MONGO_TLS_CA_FILE"),
        ]
        : [];

    $manager = new \MongoDB\Driver\Manager($uri, $options);
    $command = new \MongoDB\Driver\Command(['ping' => 1]);
    $manager->executeCommand('admin', $command);
} catch (\Throwable $e) {
    http_response_code(503);
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Error</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 { color: #dc3545; margin-bottom: 1rem; }
        p { color: #6c757d; line-height: 1.5; }
        .panda-logo {
            width: 150px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="/upload/panda.jpg" alt="Panda Logo" class="panda-logo">
        <h1>Database Connection Error</h1>
        <p>We're experiencing technical difficulties. Please try again later.</p>
    </div>
</body>
</html>
HTML;
    die();
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
