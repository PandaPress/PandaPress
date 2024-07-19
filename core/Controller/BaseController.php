<?php

namespace Panda\Controller;


use Latte\Engine;


class BaseController
{
    protected $theme_dir;
    protected $template_engine;

    public function __construct()
    {
        $this->template_engine = new Engine();

        $theme = $_ENV['CURRENT_THEME'] || "bear";

        $this->theme_dir = themes_dir() . "/" . $theme;
        $this->template_engine->setTempDirectory(root() . "/cache/templates");
    }
}
