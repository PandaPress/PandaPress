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
        global $pandadb;
        $postCount = $pandadb->selectCollection("posts")->countDocuments();
        return $this->template_engine->render("$this->views/index.latte", ["postCount" => $postCount]);
    }
}
