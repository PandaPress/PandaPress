<?php

namespace Panda\Admin\Controllers;

use MongoDB\Exception\Exception;

class PostController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->template_engine->render("$this->views/posts/index.latte");
    }

    public function compose()
    {
        return $this->template_engine->render("$this->views/posts/compose.latte");
    }

    public function save()
    {
        try {
            global $pandadb;
            $title = $_POST["title"];
            $content = $_POST["content"];

            $pandadb->selectCollection("posts")->insertOne();
            return $this->template_engine->render("$this->views/success.latte");
        } catch (Exception $e) {
            return $this->template_engine->render("$this->views/error.latte", ["error" => $e->getMessage()]);
        }
    }
}
