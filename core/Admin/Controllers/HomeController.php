<?php

namespace Panda\Admin\Controllers;

use Latte\Engine;

class HomeController
{
    private $latte;

    public function __construct()
    {
        $this->latte = new Engine();

        $this->latte->setTempDirectory(root() . "/cache/admin/templates");
    }
    public function index()
    {
        echo "admin view";
    }
}
