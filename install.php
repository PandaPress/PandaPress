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

if (env("SITE_READY", true)) {
    header("Location: /");
    exit();
}

use MongoDB\Client as MongoDBClient;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['DATABASE_TYPE']) && $_POST['DATABASE_TYPE'] != 'mongodb') {
        echo '<div class="alert alert-danger" role="alert">Unsupported database type</div>';
        exit();
    }

    if (!isset($_POST['DATABASE_TYPE']) || !isset($_POST['DATABASE_NAME']) || !isset($_POST['MONGO_URI']) || !isset($_POST['MONGO_TLS_CA_FILE']) || !isset($_POST['CURRENT_THEME']) || !isset($_POST['JWT_SECRET']) || !isset($_POST['JWT_ALGORITHM']) || !isset($_POST['JWT_ISSUER']) || !isset($_POST['JWT_AUDIENCE']) || !isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password'])) {
        die("Missing required fields");
    }

    $DATABASE_TYPE = $_POST['DATABASE_TYPE'];
    $DATABASE_NAME = $_POST['DATABASE_NAME'];
    $MONGO_URI = $_POST['MONGO_URI'];
    $MONGO_TLS_CA_FILE = $_POST['MONGO_TLS_CA_FILE'];
    $CURRENT_THEME = $_POST['CURRENT_THEME'];
    $JWT_SECRET = $_POST['JWT_SECRET'];
    $JWT_ALGORITHM = $_POST['JWT_ALGORITHM'];
    $JWT_ISSUER = $_POST['JWT_ISSUER'];
    $JWT_AUDIENCE = $_POST['JWT_AUDIENCE'];

    // cursor ide: please do not auto change this line
    $ALLOW_SIGNUP = isset($_POST['ALLOW_SIGNUP']) ? $_POST['ALLOW_SIGNUP'] : 'false';

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $mongo_client = new MongoDBClient($MONGO_URI, [
            'tls' => true,
            'tlsCAFile' => $MONGO_TLS_CA_FILE,
        ]);
        $mongo_client->selectDatabase('admin')->command(['ping' => 1]);

        $databaseName = $DATABASE_NAME;
        $pandapressdb = $mongo_client->selectDatabase($databaseName);
        $collections = iterator_to_array($pandapressdb->listCollectionNames());
        $usersCollection = $pandapressdb->selectCollection('users');

        foreach (MONGO_DEFAULT_COLLECTIONS as $collection) {
            if (!in_array($collection, $collections)) {
                $pandapressdb->createCollection($collection);
            }
        }


        $success = file_put_contents(PANDA_ROOT . '/.env', "DATABASE_TYPE=$DATABASE_TYPE\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "DATABASE_NAME=$DATABASE_NAME\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "MONGO_URI=$MONGO_URI\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "MONGO_TLS_CA_FILE=$MONGO_TLS_CA_FILE\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "CURRENT_THEME=$CURRENT_THEME\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "JWT_SECRET=$JWT_SECRET\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "JWT_ALGORITHM=$JWT_ALGORITHM\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "JWT_ISSUER=$JWT_ISSUER\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "JWT_AUDIENCE=$JWT_AUDIENCE\n", FILE_APPEND);
        $success = $success && file_put_contents(PANDA_ROOT . '/.env', "ALLOW_SIGNUP=$ALLOW_SIGNUP\n", FILE_APPEND);

        $existingAdmin = $usersCollection->findOne(['role' => 'admin']);

        if ($existingAdmin) {
            // Update existing admin user
            $result = $usersCollection->updateOne(
                ['role' => 'admin'],
                ['$set' => [
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT)
                ]]
            );
            $success = $success && $result->getModifiedCount() > 0;
        } else {
            // Insert new admin user
            $result = $usersCollection->insertOne([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => 'admin',
            ]);
            $success = $success && $result->getInsertedCount() > 0;
        }

        // Check for the special "Uncategorized" category
        // We use _id of 0 for this category as it serves as the default category
        // This allows for easy reference and ensures it's always the first category
        $uncategorizedCategory = $pandapressdb->selectCollection('categories')->findOne(['_id' => 0, 'title' => 'Uncategorized', 'slug' => 'uncategorized']);
        if (!$uncategorizedCategory) {
            $insert_category_result = $pandapressdb->selectCollection('categories')->insertOne([
                '_id' => 0,
                'title' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Uncategorized posts, default category',
            ]);
            $success = $success && $insert_category_result->getInsertedCount() > 0;
        }

        if ($success) {
            file_put_contents(PANDA_ROOT . '/.env', "SITE_READY=true\n", FILE_APPEND);
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
                        <input type="radio" name="DATABASE_TYPE" id="mongodb" value="mongodb" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="mysql" class="flex w-48">
                            MySQL (coming soon)
                        </label>
                        <input type="radio" name="DATABASE_TYPE" id="mysql" value="mysql" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" disabled />
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="DATABASE_NAME">
                    <h2 class="text-xl font-semibold">Database name</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="DATABASE_NAME" id="DATABASE_NAME" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="pandapress">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="MONGO_TLS_CA_FILE">
                    <h2 class="text-xl font-semibold">MongoDB TLS CA File</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="MONGO_TLS_CA_FILE" id="MONGO_TLS_CA_FILE" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="./ssl/isrgrootx1.pem">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="MONGO_URI">
                    <h2 class="text-xl font-semibold">MongoDB URI</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="MONGO_URI" id="MONGO_URI" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="CURRENT_THEME">
                    <h2 class="text-xl font-semibold">Current theme</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="CURRENT_THEME" id="CURRENT_THEME" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="papermod">
                </div>
            </div>

            <div class="flex flex-col">
                <label for="JWT_SECRET">
                    <h2 class="text-xl font-semibold">JWT Secret</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="JWT_SECRET" id="JWT_SECRET" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="JWT_ALGORITHM">
                    <h2 class="text-xl font-semibold">JWT Algorithm</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="JWT_ALGORITHM" id="JWT_ALGORITHM" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="HS256" readonly>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="JWT_ISSUER">
                    <h2 class="text-xl font-semibold">JWT Issuer</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="JWT_ISSUER" id="JWT_ISSUER" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="pandapress.org">
                </div>
            </div>
            <div class="flex flex-col">
                <label for="JWT_AUDIENCE">
                    <h2 class="text-xl font-semibold">JWT Audience</h2>
                </label>
                <div class="my-4  flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input type="text" name="JWT_AUDIENCE" id="JWT_AUDIENCE" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" value="pandapress.org">
                </div>
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
                        <label for="ALLOW_SIGNUP" class="flex w-48">
                            Allow
                        </label>
                        <input type="radio" name="ALLOW_SIGNUP" id="ALLOW_SIGNUP" value="true" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="ALLOW_SIGNUP" class="flex w-48">
                            Disallow
                        </label>
                        <input type="radio" name="ALLOW_SIGNUP" id="DISALLOW_SIGNUP" value="false" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
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