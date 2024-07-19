<?php

namespace Panda\Controllers;


class HomeController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        return  $this->template_engine->render($this->theme_dir . "/index.latte", ['name' => 'Panda']);
    }

    public function about()
    {
        return  $this->template_engine->render($this->theme_dir . "/about.latte", ['name' => 'about']);
    }
}
