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
        "current_theme_views" => PANDA_THEMES . "/$current_theme/Views",
        "current_theme_controllers" => PANDA_THEMES . "/$current_theme/Controllers",
    ];
}


function get_form_old_value($session_key, $field) {
    return isset($_SESSION[$session_key][$field]) ? htmlspecialchars($_SESSION[$session_key][$field]) : '';
}

function unset_session_keys($kees = []) {
    foreach ($kees as $kee) {
        unset($_SESSION[$kee]);
    }
}

// plugins helper functions