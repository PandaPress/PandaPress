<?php

namespace Panda\Controllers;


use Latte\Engine;


class BaseController
{
    protected $theme_dir;
    protected $template_engine;

    public function __construct()
    {
        // template engine
        $this->template_engine = new Engine();

        // set template engine template directory
        $cache_panda_tmpl_dir =   root() . "/cache/panda/templates";
        if (!is_dir($cache_panda_tmpl_dir)) {
            mkdir($cache_panda_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_panda_tmpl_dir);

        // current theme
        $theme = $_ENV['CURRENT_THEME'] ?? "bear";
        $this->theme_dir = themes_dir() . "/" . $theme;
    }
}
