<?php


define("PANDA_THEME_ROUTES", [
    ["GET", "/", "\Panda\Theme\Controllers\HomeController", "index"],
    ["GET", "/about", "\Panda\Theme\Controllers\HomeController", "about"],
]);
