<?php


define("PANDA_ROOT", __DIR__);

define("PANDA_THEMES", __DIR__ . '/themes');
define("PANDA_PLUGINS", __DIR__ . '/plugins');

define("MONGO_DEFAULT_COLLECTIONS",    [
    "pages",
    'posts',
    'categories',
    'tags',
    'users',
    'comments',
    'options',
]);

define("PANDA_GET_ROUTES", [
    ["/", "\Panda\Controllers\HomeController", "index"],
    ["/", "\Panda\Controllers\HomeController", "about"],
]);

define("PANDA_ADMIN_GET_ROUTES", [
    ['/', '\Panda\Admin\Controllers\HomeController', 'index'],
    ['/compose', '\Panda\Admin\Controllers\PostController', 'compose'],
    ['/save', '\Panda\Admin\Controllers\PostController', 'save']
]);
