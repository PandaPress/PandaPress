<?php

namespace Panda;

use Bramus\Router as BramusRouter;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Router {
    private $router;


    public function __construct() {
        $this->router = new BramusRouter();
        $this->before();
        $this->setRoutes();
    }

    public function before() {

        // TODO: modify the Bramus\Router class to allow before() to be run multiple times
        $this->router->before('*', '/.*', function () {
            header('X-Powered-By: Panda CMS');

            $path = $_SERVER['REQUEST_URI'];

            $user_id = \Panda\Session::get('user_id');
            $jwt_from_cookie = $_COOKIE['panda_token'] ?? null;
            $decoded_jwt = $jwt_from_cookie ? $this->verify_jwt($jwt_from_cookie) : null;

            if (str_starts_with($path, '/admin')) {
                if (!isset($user_id) || !$decoded_jwt || $user_id !== $decoded_jwt['data']['id'] || $decoded_jwt['data']['role'] !== 'admin') {
                    header('Location: /login');
                    exit();
                }
            }

            if (str_starts_with($path, '/login') || str_starts_with($path, '/signup')) {
                if ($decoded_jwt && $decoded_jwt['data']['id'] === $user_id && $decoded_jwt['data']['role'] === 'admin') {
                    header('Location: /');
                    exit();
                }
            }
        });
    }

    public function setRoutes() {

        $_router = $this->router;

        foreach (PANDA_THEME_ROUTES as $theme_route) {
            [$method, $route, $controller, $func] = $theme_route;
            if ($method === "GET") {
                $_router->get($route, "$controller@$func");
            }
            if ($method === "POST") {
                $_router->post($route, "$controller@$func");
            }
        }

        foreach (PANDA_ADMIN_ROUTES as $admin_route) {
            [$method, $route, $controller, $func] = $admin_route;
            if ($method === "GET") {
                $_router->get($route, "$controller@$func");
            }
            if ($method === "POST") {
                $_router->post($route, "$controller@$func");
            }
        }

        $_router->get("/install", function () {
            if (env("SITE_READY")) {
                header("Location: /");
                exit();
            }
            header("Location: /install.php");
            exit();
        });

        // TODO: allow cms owners to customize it
        // if no customized, default to Panda 404
        $_router->set404('\Panda\Controllers\ErrorController@notFound');
    }

    public function simpleRedirect(string $url, array $data = []) {

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $_SESSION["panda_$key"] = $value;
            }
        }

        header("Location: $url");
        exit();
    }


    public function run() {
        $this->router->run();
    }

    // if jwt is expired, JWT::decode will throw an exception, ExpiredException
    public function verify_jwt($jwt) {
        if (!$jwt) {
            return false;
        }

        $secret_key = env("JWT_SECRET");
        $algorithm = env("JWT_ALGORITHM");
        try {
            $headers = new \stdClass();
            $token = JWT::decode($jwt, new Key($secret_key, $algorithm), $headers);
            return json_decode(json_encode($token), true);
        } catch (\Throwable $e) {
            global $logger;
            $logger->error($e->getMessage());
            return false;
        }
    }
}
