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

        $user = match ($is_email) {
            true => $userCollection->findOne(['email' => $username]),
            false => $userCollection->findOne(['username' => $username]),
        };

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
        if (strcasecmp(env('ALLOW_SIGNUP'), 'true') == 0) {
            return $this->template_engine->render(PANDA_ROOT . '/core/Views/auth/signup.latte');
        }
        // go to 404
        global $router;
        return $router->simpleRedirect('/404');
    }

    // !TODO  protect it from spam, bots, brute force, etc.
    // !TODO  owners can disallow signup from ip, email, domain, etc. or disallow all signups
    public function signupApi() {
        global $pandadb;

        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];

        $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$is_email) {
            return $this->json([
                'message' => 'Invalid email',
                'success' => false,
                'code' => 400,
                'data' => null
            ]);
        }

        if ($password !== $password2) {
            return $this->json([
                'message' => 'Passwords do not match',
                'success' => false,
                'code' => 400,
                'data' => null
            ]);
        }

        $userCollection = $pandadb->selectCollection('users');

        $user = $userCollection->findOne(
            [
                '$or' => [
                    ['email' => $username],
                    ['username' => $username]
                ]
            ]
        );

        if ($user) {
            return $this->json([
                'message' => 'Username already exists',
                'success' => false,
                'code' => 400,
                'data' => null
            ]);
        }

        $user = $userCollection->insertOne([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'role' => 'user',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        return $this->json([
            'message' => 'User created successfully',
            'success' => true,
            'code' => 201,
            'data' => [
                'id' => $user->getInsertedId()->__toString(),
            ]
        ]);
    }

    public function logout() {
    }
}
