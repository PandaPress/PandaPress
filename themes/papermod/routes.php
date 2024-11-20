<?php


define("PANDA_THEME_ROUTES", [
    ["GET", "/", "\Panda\Theme\Controllers\HomeController", "index"],
    ["GET", "/about", "\Panda\Theme\Controllers\HomeController", "about"],

    // pages in the header
    ["GET", "/archive", "\Panda\Theme\Controllers\PostController", "archive"],
    ["GET", "/search", "\Panda\Theme\Controllers\PostController", "search"],
    ["GET", "/tags", "\Panda\Theme\Controllers\PostController", "tags"],
    ["GET", "/home", "\Panda\Theme\Controllers\HomeController", "home"],
    ["GET", "/profile", "\Panda\Theme\Controllers\HomeController", "profile"],

    // single post
    ["GET", "/post/{slug}", "\Panda\Theme\Controllers\PostController", "show"],
]);
