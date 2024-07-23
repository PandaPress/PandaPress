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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">
</head>

<body>
    <div class="container">
        <header class="d-flex justify-content-center py-3 border-bottom">
            <h1>Welcome to Panda CMS</h1>
        </header>

        <form class="mt-3" action="install.php" method="post">
            <div class="form-group">
                <h2>Choose a database</h2>
                <div class="input-group mb-3 d-flex flex-column">
                    <div class="form-check form-check-inline">
                        <label for="mongodb" class="form-check-label w-25">
                            MongoDB
                        </label>
                        <input type="radio" name="database" id="mongodb" class="form-check-input" />
                    </div>

                    <div class="form-check form-check-inline">
                        <label for="mysql" class="form-check-label w-25">
                            MySQL
                        </label>
                        <input type="radio" name="database" id="mysql" class="form-check-input" disabled />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="envvar">
                    <h2>Set environment variables</h2>
                </label>
                <textarea rows="10" cols="150" type="text" class="form-control" id="envvar" name="envvar">
CURRENT_THEME=papermod
MONGO_URI=
MONGO_TLS_CA_FILE=/etc/ssl/cert.pem
SITE_READY=true
                </textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>

</html>