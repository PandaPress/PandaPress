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
        $commentCount = $pandadb->selectCollection("comments")->countDocuments();

        return $this->template_engine->render("$this->views/index.latte", [
            "postCount" => $postCount, 
            "commentCount" => $commentCount
        ]);
    }
}
