<?php




function env(string $kee, bool $is_bool = false) {

    if (!in_array($kee, PANDA_ENV_KEYS)) {
        throw new \Exception("Invalid environment variable key: $kee");
    }

    $value = isset($_ENV[$kee]) ? $_ENV[$kee] : null;
    if ($is_bool) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    return $value;
}

// themes helper functions




function get_form_old_value($session_key, $field) {
    $kee = generate_full_session_key($session_key);
    return isset($_SESSION[$kee][$field]) ? htmlspecialchars($_SESSION[$kee][$field]) : '';
}

function generate_full_session_key(string $kee) {
    return "panda_$kee";
}

function unset_session_keys($kees = []) {
    foreach ($kees as $kee) {
        $_kee = generate_full_session_key($kee);
        unset($_SESSION[$_kee]);
    }
}

// plugins helper functions