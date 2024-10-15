<?php

declare(strict_types=1);

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

if (isset($_ENV["SITE_READY"])) {
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
        ]);
        $mongo_client->selectDatabase('admin')->command(['ping' => 1]);

        $databaseName = $envvars["DATABASE_NAME"];
        $pandapressdb = $mongo_client->selectDatabase($databaseName);
        $collections = iterator_to_array($pandapressdb->listCollectionNames());
        $usersCollection = $pandapressdb->selectCollection('users');

        foreach (MONGO_DEFAULT_COLLECTIONS as $collection) {
            if (!in_array($collection, $collections)) {
                $pandapressdb->createCollection($collection);
            }
        }

        $success = file_put_contents(PANDA_ROOT . '/.env',  $_POST['envvar']);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "SITE_READY=true\n", FILE_APPEND);

        $database = $_POST['database'];
        $allow_signup = $_POST['allow_signup'];
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "DB_TYPE=$database\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "DATABASE_NAME=$databaseName\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "ALLOW_SIGNUP=$allow_signup\n", FILE_APPEND);

        $existingAdmin = $usersCollection->findOne(['role' => 'admin']);

        if ($existingAdmin) {
            // Update existing admin user
            $result = $usersCollection->updateOne(
                ['role' => 'admin'],
                ['$set' => [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
                ]]
            );
            $success = $success && $result->getModifiedCount() > 0;
        } else {
            // Insert new admin user
            $result = $usersCollection->insertOne([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                'role' => 'admin',
            ]);
            $success = $success && $result->getInsertedCount() > 0;
        }

        $uncategorizedCategory = $pandapressdb->selectCollection('categories')->findOne(['title' => 'Uncategorized']);
        if ($uncategorizedCategory) {
            $uncategorizedCategoryId = $uncategorizedCategory['_id']->__toString();
            $success = $success && file_put_contents(PANDA_ROOT . '/.env', "UNCATEGORIZED_CATEGORY_ID=$uncategorizedCategoryId\n", FILE_APPEND);
        } else {
            $result2 = $pandapressdb->selectCollection('categories')->insertOne([
                'title' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Uncategorized posts, default category',
            ]);
            $success = $success && $result2->getInsertedCount() > 0;
            $uncategorizedCategoryId = $result2->getInsertedId()->__toString();
            $success = $success && file_put_contents(PANDA_ROOT . '/.env', "UNCATEGORIZED_CATEGORY_ID=$uncategorizedCategoryId\n", FILE_APPEND);
        }

        if ($success) {
            header("Location: /");
        } else {
            echo '<div class="alert alert-danger" role="alert">Error during installation</div>';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panda Press Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
</head>

<body class="flex justify-center">
    <div class="container max-w-screen-lg mb-20">
        <div class="navbar bg-base-100 w-full border-solid border-0 border-b border-blue-900
">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl">Installation</a>
            </div>
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li><a href="https://pandapress.org" target="_blank">Panda Press</a></li>
                </ul>
            </div>
        </div>

        <form class="w-full px-5 mt-8" action="install.php" method="post">
            <div class="flex flex-col">
                <h2 class="text-xl font-semibold">Choose a database</h2>
                <div class="flex flex-col my-4">
                    <div class="flex items-center">
                        <label for="mongodb" class="flex w-48">
                            MongoDB
                        </label>
                        <input type="radio" name="database" id="mongodb" value="mongodb" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="mysql" class="flex w-48">
                            MySQL (coming soon)
                        </label>
                        <input type="radio" name="database" id="mysql" value="mysql" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" disabled />
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="envvar">
                    <h2 class="text-xl font-semibold">Set environment variables</h2>
                </label>
                <textarea id="envvar" name="envvar" rows="10" type="text" class="my-4 p-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
CURRENT_THEME=papermod

MONGO_URI=
MONGO_TLS_CA_FILE=./ssl/isrgrootx1.pem
DATABASE_NAME=pandapress


JWT_SECRET=
JWT_ALGORITHM=HS256
JWT_ISSUER=
JWT_AUDIENCE=
</textarea>
            </div>
            <div class="flex flex-col">
                <label for="username">
                    <h2 class="text-xl font-semibold">Admin username</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="username" id="username" autocomplete="username" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="janesmith">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="email">
                    <h2 class="text-xl font-semibold">Admin email</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="email" name="email" id="email" autocomplete="email" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="janesmith@example.com">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="username">
                    <h2 class="text-xl font-semibold">Admin password</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="password" name="password" id="password" autocomplete="password" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" placeholder="password">
                </div>
            </div>
            <div class="flex flex-col">
                <h2 class="text-xl font-semibold">Allow public signup</h2>
                <div class="flex flex-col my-4">
                    <div class="flex items-center">
                        <label for="allow_signup" class="flex w-48">
                            Allow
                        </label>
                        <input type="radio" name="allow_signup" id="allow_signup" value="true" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="allow_signup" class="flex w-48">
                            Disallow
                        </label>
                        <input type="radio" name="allow_signup" id="allow_signup" value="false" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>
                </div>
            </div>
            <div class="flex">
                <button type="submit" name="submit" class="btn btn-outline border-blue-900">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>