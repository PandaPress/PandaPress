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

define("PANDA_ADMIN_ROUTES", [
    ["GET", "/admin", "\Panda\Admin\Controllers\HomeController", "index"],
    ["GET", "/admin/posts", "\Panda\Admin\Controllers\PostController", "index"],
    ["GET", "/admin/posts/compose", "\Panda\Admin\Controllers\PostController", "compose"],
    ["POST", '/admin/posts/save', '\Panda\Admin\Controllers\PostController', 'save'],
    ["POST", '/admin/posts/update', '\Panda\Admin\Controllers\PostController', 'update'],
    ["POST", '/admin/posts/upsave', '\Panda\Admin\Controllers\PostController', 'upsave'],
    ["POST", '/admin/posts/delete', '\Panda\Admin\Controllers\PostController', 'delete'],
    ["GET", '/admin/posts/success', '\Panda\Admin\Controllers\PostController', 'success'],
    ["GET", '/admin/posts/error', '\Panda\Admin\Controllers\PostController', 'error']
]);