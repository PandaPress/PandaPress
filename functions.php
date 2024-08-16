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
    $kee = generate_full_session_key($session_key);
    return isset($_SESSION[$kee][$field]) ? htmlspecialchars($_SESSION[$kee][$field]) : '';
}

function generate_full_session_key(string $kee){
    return "panda_$kee";
}

function unset_session_keys($kees = []) {
    foreach ($kees as $kee) {
        $_kee = generate_full_session_key($kee);
        unset($_SESSION[$_kee]);
    }
}

// plugins helper functions