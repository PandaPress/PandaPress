<?php

define("PANDA_THEME_STORAGE_TYPE", \Panda\Utils\SettingStorageType::FILE);


define("PANDA_THEME_SETTINGS", [
    // ! Attention: variables starting with "theme_" are not allowed to be changed by users
    "theme_name" => "papermod",
    "theme_version" => "1.0.0",
    "theme_description" => "the theme PaperMod implementation in Panda Press",
    "theme_author" => "Yumin Gui <mail@yumin.io>",
    "theme_author_url" => "https://github.com/yumin-gui",
    "theme_homepage" => "https://github.com/pandapress/pandapress",
    "theme_license" => "GPL 2.0",
    "theme_license_url" => "https://github.com/pandapress/pandapress/blob/main/themes/papermod/LICENSE",
    "site_base_url" => "https://pandapress.org",
    "enable_profile_mode" => true,
    "site_title" => "Panda Press",
    "site_subtitle" => "a modern, fast, minimalist, and powerful CMS, with a focus on simplicity and performance.",
    "site_description" => "Panda Press is a modern, fast, minimalist, and powerful CMS, with a focus on simplicity and performance.",
    "site_header_page_links" => ["about"],
    "site_keywords" => ["Modern CMS", "PHP 8.2+", "Without Composer", "PSR-4 Autoloader", "AdminLTE Dashboard", "Powerful Plugins", "Beautiful Themes", "Open Source", "Always Free"],
    "site_author" => "Yumin",
    "show_author_in_post" => true,
    "profile_image_name" => "panda.png",
    "profile_image_title" =>  "Panda Press Logo",
    "home_page_title" => "🚀 Welcome to Panda Press and its default theme PaperMod",
    "home_page_descriptions" => [
        "Panda Press is a modern, fast, and powerful CMS platform.",
        "It uses PSR-4 Autoloader, instead of Composer, which makes it beginner-friendly.",
        "It also provides a beautiful and modern Dashboard, by using AdminLTE.",
        "Panda Press is open source and always free to use for anyone.",
        "There will be more features coming soon.",
    ],
    "social_buttons" => [
        "github" => "https://github.com/pandapress/pandapress",
        "youtube" => "https://youtube.com/@KatsuraSoftware",
        "linkedin" => "https://linkedin.com/in/guiyumin/",
        "x" => "https://x.com/guiyumin",
    ],
    "post_page_size" => 10,
    "post_excerpt_length" => 100,
    "time_zone" => "America/Los_Angeles",
    "time_format" => "g:i A",
    "date_format" => "F j, Y",
    "show_full_text_in_rss" => true,
]);


// define the i18n kv 
