<?php


define("PANDA_THEME_ROUTES", [
    ["GET", "/", "\Panda\Controllers\HomeController", "index"],
    ["GET", "/about", "\Panda\Controllers\HomeController", "about"],
]);
