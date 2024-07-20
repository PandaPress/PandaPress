<?php


function root()
{
    return __DIR__;
}

function env(string $kee)
{
    return $_ENV[$kee];
}

// themes helper functions
function themes_dir()
{
    return __DIR__ .  "/themes";
}

function get_theme_info(string $current_theme)
{
    return [
        "current_theme_dir" => themes_dir() . "/$current_theme",
        "current_theme_views" => themes_dir() . "/$current_theme/views",
    ];
}


// plugins helper functions
function plugins_dir()
{
    return __DIR__ .  "/plugins";
}
