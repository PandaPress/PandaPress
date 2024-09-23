<?php

namespace Panda\Admin\Controllers;



class HomeController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        global $pandadb;
        $postCount = $pandadb->selectCollection("posts")->countDocuments();
        $categoryCount = $pandadb->selectCollection("categories")->countDocuments();
        $commentCount = $pandadb->selectCollection("comments")->countDocuments();

        return $this->template_engine->render("$this->views/index.latte", $this->getFullDataForTemplate([
            "postCount" => $postCount,
            "categoryCount" => $categoryCount,
            "commentCount" => $commentCount,
        ]));
    }

    public function success() {
        $success_message = isset($_SESSION['panda_success_message']) ? $_SESSION['panda_success_message'] : "";
        unset($_SESSION['panda_success_message']);
        return $this->template_engine->render("$this->views/success.latte", $this->getFullDataForTemplate([
            "success_message" => $success_message
        ]));
    }

    public function error() {
        $error_message = isset($_SESSION['panda_error_message']) ? $_SESSION['panda_error_message'] : "";
        unset($_SESSION['panda_error_message']);
        return $this->template_engine->render("$this->views/error.latte", $this->getFullDataForTemplate([
            "error_message" => $error_message
        ]));
    }
}
