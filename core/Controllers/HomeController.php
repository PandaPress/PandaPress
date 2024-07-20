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
        return  $this->template_engine->render($this->current_theme_views . "/index.latte", ['name' => 'Panda']);
    }

    public function about()
    {
        return  $this->template_engine->render($this->current_theme_views . "/about.latte", ['name' => 'about']);
    }
}
