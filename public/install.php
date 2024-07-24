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

if (!file_exists($dotenv_file)) {
    touch($dotenv_file);
}

$dotenv = new Dotenv();
$dotenv->load($dotenv_file);

if (env("SITE_READY")) {
    header("Location: /");
    exit();
}

use MongoDB\Client as MongoDBClient;


if (isset($_POST['envvar'])) {
    try {
        $envvars = $dotenv->parse($_POST['envvar']);

        $mongo_client = new MongoDBClient($envvars["MONGO_URI"], [
            'tls' => true,
            'tlsCAFile' => $envvars["MONGO_TLS_CA_FILE"],
            // 'tlsAllowInvalidCertificates' => true,
            // 'tlsAllowInvalidHostnames' => true
        ]);
        $mongo_client->selectDatabase('admin')->command(['ping' => 1]);

        $success = file_put_contents(PANDA_ROOT . '/.env',  $_POST['envvar']);

        if ($success) {
            echo '<script>alert("Installation completed successfully")</script>';
            header("Location: /");
        } else {
            echo '<script>alert("Error during installation")</script>';
        }
    } catch (Exception $e) {
        $err_msg = $e->getMessage();
        echo '<div class="alert alert-danger" role="alert">' . $err_msg . '</div>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panda CMS Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
</head>

<body class="flex justify-center">
    <div class="container max-w-screen-lg">
        <div class="navbar bg-base-100 w-full">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl">Installation</a>
            </div>
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="https://pandacms.net" target="_blank">Panda CMS</a></li>
                </ul>
            </div>
        </div>

        <form class="w-full px-5" action="install.php" method="post">
            <div class="flex flex-col">
                <h2 class="text-xl font-semibold">Choose a database</h2>
                <div class="flex flex-col my-4">
                    <div class="flex items-center">
                        <label for="mongodb" class="flex w-48">
                            MongoDB
                        </label>
                        <input type="radio" name="database" id="mongodb" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="mysql" class="flex w-48">
                            MySQL (coming soon)
                        </label>
                        <input type="radio" name="database" id="mysql" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" disabled />
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="envvar">
                    <h2 class="text-xl font-semibold">Set environment variables</h2>
                </label>
                <textarea rows="10" type="text" class="my-4 p-0" id="envvar" name="envvar">
CURRENT_THEME=papermod
MONGO_URI=
MONGO_TLS_CA_FILE=/etc/ssl/cert.pem
SITE_READY=true
                </textarea>
            </div>
            <div class="flex">
                <button type="submit" name="submit" class="btn btn-outline btn-primary">Submit</button>
            </div>
        </form>
    </div>

    </div>


</body>

</html>