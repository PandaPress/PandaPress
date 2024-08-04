<?php

namespace  Panda\Controllers;

class ErrorController extends BaseController {
    public function __construct() {
        parent::__construct();
    }


    public function notFound() {
        return $this->template_engine->render(PANDA_ROOT . '/core/Views/notfound.latte');
    }
    
}