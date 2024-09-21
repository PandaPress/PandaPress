<?php

namespace Panda;

use Bramus\Router as BramusRouter;

class Router {
    private $router;


    public function __construct() {
        $this->router = new BramusRouter();

        $this->router->before('*', '/.*', function () {
            header('X-Powered-By: Panda CMS');

            $path = $_SERVER['REQUEST_URI'];
            if (strpos($path, '/admin') === 0) {
                $user_id = \Panda\Session::get('user_id');

                if (!isset($user_id) || !isset($_COOKIE['panda_token'])) {

                    // TODO: check if user_id equals id in token
                    // TODO: check if token is not expired

                    header('Location: /login');
                    exit();
                }
            }
        });

        $this->setRoutes();
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
}
