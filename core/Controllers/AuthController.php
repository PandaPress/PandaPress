<?php

namespace  Panda\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function login() {
        return $this->template_engine->render(PANDA_ROOT . '/core/Views/auth/login.latte');
    }

    public function loginApi() {
        global $pandadb;
        $userCollection = $pandadb->selectCollection('users');

        $username = $_POST['username'];
        $password = $_POST['password'];

        $is_email = filter_var($username, FILTER_VALIDATE_EMAIL);

        $user = $is_email ?
            $userCollection->findOne(['email' => $username]) :
            $userCollection->findOne(['username' => $username]);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->json([
                'message' => 'Invalid username or password',
                'success' => false,
                'code' => 401,
                'data' => null
            ]);
        }

        $key = env('JWT_SECRET');
        $payload = [
            'iss' => env('JWT_ISSUER'),
            'aud' => env('JWT_AUDIENCE'),
            'iat' => time(),
            'exp' => time() + 3600 * 24 * 30 * 1, // 1 month
            'data' => [
                'id' => $user['_id']->__toString(),
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ]
        ];

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, env('JWT_ALGORITHM'));


        \Panda\Session::set('user_id', $user['_id']->__toString());
        \Panda\Session::set('user_token', $jwt);

        \Panda\Cookie::setcookie('panda_token', $jwt);

        return $this->json([
            'message' => 'Login successful',
            'success' => true,
            'code' => 200,
            'data' => [
                'id' => $user['_id']->__toString(),
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'token' => $jwt
            ]
        ]);
    }

    public function signup() {
        return $this->template_engine->render(PANDA_ROOT . '/core/Views/auth/signup.latte');
    }

    public function signupApi() {
    }

    public function logout() {
    }
}
