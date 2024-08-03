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
    ["POST", '/admin/posts/save', '\Panda\Admin\Controllers\PostController', 'save']
]);