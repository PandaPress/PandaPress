<?php

namespace Panda\Admin\Controllers;


use Latte\Engine;


class BaseController
{
    protected $views;
    protected $template_engine;

    public function __construct()
    {
        // template engine
        $this->template_engine = new Engine();
        $cache_admin_tmpl_dir =   PANDA_ROOT . "/cache/admin/templates";
        if (!is_dir($cache_admin_tmpl_dir)) {
            mkdir($cache_admin_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_admin_tmpl_dir);

        // admin view templates
        $this->views = PANDA_ROOT . "/core/Admin/Views";
    }
}
