<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;

class HomeController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->template_engine->render("$this->views/index.latte", ["postCount" => 123]);
    }
}
