<?php

namespace  Panda\Controllers;

class AuthController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function login() {
        return $this->template_engine->render(PANDA_ROOT . '/core/Views/auth/login.latte');
    }

    public function loginApi() {
    }

    public function signup() {
        return $this->template_engine->render(PANDA_ROOT . '/core/Views/auth/signup.latte');
    }

    public function signupApi() {
    }

    public function logout() {
    }
}
