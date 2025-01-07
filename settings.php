<?php


define("PANDA_ROOT", __DIR__);
define("PANDA_ROOT_PUBLIC", __DIR__ . "/public");

define("PANDA_THEMES", __DIR__ . '/themes');
define("PANDA_PLUGINS", __DIR__ . '/plugins');

define("MONGO_DEFAULT_COLLECTIONS",    [
    'posts',
    'categories',
    'tags',
    'users',
    'comments',
    'settings',
]);

define("PANDA_ADMIN_ROUTES", [

    ['GET', '/login', '\Panda\Controllers\AuthController', 'login'],
    ['POST', '/login', '\Panda\Controllers\AuthController', 'loginApi'],
    ['GET', '/signup', '\Panda\Controllers\AuthController', 'signup'],
    ['POST', '/signup', '\Panda\Controllers\AuthController', 'signupApi'],
    ['POST', '/logout', '\Panda\Controllers\AuthController', 'logout'],
    ['GET', '/404', '\Panda\Controllers\ErrorController', 'notFound'],

    // admin home
    ["GET", "/admin", "\Panda\Admin\Controllers\HomeController", "index"],
    ["GET", '/admin/success', '\Panda\Admin\Controllers\HomeController', 'success'],
    ["GET", '/admin/error', '\Panda\Admin\Controllers\HomeController', 'error'],

    // admin posts
    ["GET", "/admin/posts", "\Panda\Admin\Controllers\PostController", "index"],
    ["GET", "/admin/posts/compose", "\Panda\Admin\Controllers\PostController", "compose"],
    ["POST", '/admin/posts/create', '\Panda\Admin\Controllers\PostController', 'create'],
    ["GET", '/admin/posts/update/{id}', '\Panda\Admin\Controllers\PostController', 'update'],
    ["POST", '/admin/posts/upsave', '\Panda\Admin\Controllers\PostController', 'upsave'],
    ["POST", '/admin/posts/delete', '\Panda\Admin\Controllers\PostController', 'delete'],
    ["GET", "/admin/posts/tags", "\Panda\Admin\Controllers\PostController", "tags"],
    ["GET", "/admin/posts/tags/{tag}", "\Panda\Admin\Controllers\PostController", "tag"],
    ["POST", "/admin/posts/tags/remove4all", "\Panda\Admin\Controllers\PostController", "removeTag4All"],
    ["POST", "/admin/posts/tags/remove4one", "\Panda\Admin\Controllers\PostController", "removeTag4One"],
    ["GET", "/admin/pages", "\Panda\Admin\Controllers\PostController", "pages"],


    // admin categories
    ["GET", "/admin/categories", "\Panda\Admin\Controllers\CategoryController", "index"],
    ["GET", "/admin/categories/create", "\Panda\Admin\Controllers\CategoryController", "create"],
    ["POST", "/admin/categories/delete", "\Panda\Admin\Controllers\CategoryController", "delete"],
    ["POST", '/admin/categories/save', '\Panda\Admin\Controllers\CategoryController', 'save'],
    ["GET", '/admin/categories/update/{id}', '\Panda\Admin\Controllers\CategoryController', 'update'],
    ["POST", '/admin/categories/upsave', '\Panda\Admin\Controllers\CategoryController', 'upsave'],

    // sessions
    ["POST", '/admin/session/clear', '\Panda\Admin\Controllers\SessionController', 'clear'],

    // themes
    ["GET", "/admin/themes", "\Panda\Admin\Controllers\ThemeController", "index"],
    ["GET", "/admin/themes/settings", "\Panda\Admin\Controllers\ThemeController", "settings"],
    ["POST", "/admin/themes/current", "\Panda\Admin\Controllers\ThemeController", "current"],


]);


define("PANDA_ENV_KEYS", [
    'DATABASE_TYPE',
    'DATABASE_NAME',
    'MONGO_URI',
    'MONGO_TLS_CA_FILE',
    'CURRENT_THEME',
    'JWT_SECRET',
    'JWT_ALGORITHM',
    'JWT_ISSUER',
    'JWT_AUDIENCE',
    'ALLOW_SIGNUP',
    'SITE_READY',
    'APP_ENV',
]);
