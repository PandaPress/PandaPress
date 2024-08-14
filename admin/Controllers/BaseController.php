<?php

namespace Panda\Admin\Controllers;


use Latte\Engine;
use Latte\Essential\RawPhpExtension;



class BaseController
{
    protected $views;
    protected $template_engine;

    public function __construct()
    {
        // template engine
        $this->template_engine = new Engine();
        $cache_admin_tmpl_dir =   PANDA_ROOT . "/cache/templates/admin";
        if (!is_dir($cache_admin_tmpl_dir)) {
            mkdir($cache_admin_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_admin_tmpl_dir);
        $this->template_engine->addExtension(new RawPhpExtension);

        // admin view templates
        $this->views = PANDA_ROOT . "/admin/Views";

    }
}
