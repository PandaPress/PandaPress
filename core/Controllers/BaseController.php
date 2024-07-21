<?php

namespace Panda\Controllers;


use Latte\Engine;


class BaseController
{

    protected $template_engine;
    protected $current_theme;
    protected $current_theme_dir;
    protected $current_theme_views;


    public function __construct()
    {
        // template engine
        $this->template_engine = new Engine();
        $this->current_theme = env("CURRENT_THEME") ?? "jasmine";
        $this->current_theme_dir = get_theme_info($this->current_theme)['current_theme_dir'];
        $this->current_theme_views = get_theme_info($this->current_theme)['current_theme_views'];

        // set template engine template directory
        $cache_panda_tmpl_dir = PANDA_ROOT . "/cache/templates/$this->current_theme";
        if (!is_dir($cache_panda_tmpl_dir)) {
            mkdir($cache_panda_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_panda_tmpl_dir);
    }
}
