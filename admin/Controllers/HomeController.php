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

    public function success(){
        $success_message = isset($_SESSION['panda_success_message']) ? $_SESSION['panda_success_message'] : "";
        unset($_SESSION['panda_success_message']);
        return $this->template_engine->render("$this->views/success.latte", [
            "success_message" => $success_message
        ]);
    }

    public function error(){
        $error_message = isset($_SESSION['panda_error_message']) ? $_SESSION['panda_error_message'] : "";
        unset($_SESSION['panda_error_message']);
        return $this->template_engine->render("$this->views/error.latte", [
            "error_message" => $error_message
        ]);
    }
}
