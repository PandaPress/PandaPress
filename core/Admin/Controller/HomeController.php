<?php

namespace Panda\Admin\Controller;

use Latte\Engine;

class HomeController
{
    private $latte;
    private $theme_dir;


    public function __construct()
    {
        $this->latte = new Engine();
        $this->theme_dir = root() . "/core/Admin/View";
        $this->latte->setTempDirectory(root() . "/cache/admin/templates");
    }
    public function index()
    {
        echo "admin view";
    }
}
