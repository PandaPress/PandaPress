<?php

namespace Panda\Controllers;


use Latte\Engine;


class BaseController
{
    protected $theme;
    protected $theme_dir;
    protected $theme_views;
    protected $template_engine;

    public function __construct()
    {
        // template engine
        $this->template_engine = new Engine();


        // current theme
        $this->theme = env('CURRENT_THEME') ?? "bear";
        $this->theme_dir = themes_dir() . "/" . $this->theme;
        $this->theme_views = $this->theme_dir . "/views";

        // set template engine template directory
        $cache_panda_tmpl_dir = root() . "/cache/templates/" . $this->theme;
        if (!is_dir($cache_panda_tmpl_dir)) {
            mkdir($cache_panda_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_panda_tmpl_dir);
    }
}
