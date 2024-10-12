<?php

namespace Panda\Theme\Controllers;

use Panda\Controllers\BaseController;


class HomeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }
    public function index() {
        return  $this->template_engine->render($this->current_theme_views . "/index.latte", $this->appendMetaData());
    }

    public function about() {
        return  $this->template_engine->render($this->current_theme_views . "/about.latte", $this->appendMetaData());
    }
}
