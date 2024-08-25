<?php


define("PANDA_ROOT", __DIR__);

define("PANDA_THEMES", __DIR__ . '/themes');
define("PANDA_PLUGINS", __DIR__ . '/plugins');

define("MONGO_DEFAULT_COLLECTIONS",    [
    'posts',
    'categories',
    'tags',
    'users',
    'comments',
    'options',
]);

define("PANDA_ADMIN_ROUTES", [
    // home
    ["GET", "/admin", "\Panda\Admin\Controllers\HomeController", "index"],
    ["GET", '/admin/success', '\Panda\Admin\Controllers\HomeController', 'success'],
    ["GET", '/admin/error', '\Panda\Admin\Controllers\HomeController', 'error'],

    // posts
    ["GET", "/admin/posts", "\Panda\Admin\Controllers\PostController", "index"],
    ["GET", "/admin/posts/compose", "\Panda\Admin\Controllers\PostController", "compose"],
    ["POST", '/admin/posts/save', '\Panda\Admin\Controllers\PostController', 'save'],
    ["GET", '/admin/posts/update/{id}', '\Panda\Admin\Controllers\PostController', 'update'],
    ["POST", '/admin/posts/upsave', '\Panda\Admin\Controllers\PostController', 'upsave'],
    ["POST", '/admin/posts/delete', '\Panda\Admin\Controllers\PostController', 'delete'],

    // categories
    ["GET", "/admin/categories", "\Panda\Admin\Controllers\CategoryController", "index"],
    ["GET", "/admin/categories/create", "\Panda\Admin\Controllers\CategoryController", "create"],
    ["POST", "/admin/categories/delete", "\Panda\Admin\Controllers\CategoryController", "delete"],
    ["POST", '/admin/categories/save', '\Panda\Admin\Controllers\CategoryController', 'save'],
    ["GET", '/admin/categories/update/{id}', '\Panda\Admin\Controllers\CategoryController', 'update'],
    ["POST", '/admin/categories/upsave', '\Panda\Admin\Controllers\CategoryController', 'upsave'],

    // sessions
    ["POST", '/admin/session/clear', '\Panda\Admin\Controllers\SessionController', 'clear']
]);