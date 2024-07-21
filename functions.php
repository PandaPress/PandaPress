<?php




function env(string $kee)
{
    return $_ENV[$kee];
}

// themes helper functions


function get_theme_info(string $current_theme)
{
    return [
        "current_theme_dir" => PANDA_THEMES . "/$current_theme",
        "current_theme_views" => PANDA_THEMES . "/$current_theme/views",
    ];
}


// plugins helper functions