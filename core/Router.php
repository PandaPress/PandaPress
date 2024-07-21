<?php

namespace Panda;

use Bramus\Router as BramusRouter;

class Router
{
    private $router;


    public function __construct()
    {
        $this->router = new BramusRouter();
        $this->setRoutes();
    }

    public function setRoutes()
    {

        $_router = $this->router;

        foreach (PANDA_GET_ROUTES as $get_route) {
            [$route, $controller, $func] = $get_route;
            $_router->get($route, "$controller@$func");
        }

        $_router->get("/install", function () {
            if (env("SITE_READY")) {
                header("Location: /");
                exit();
            }
            header("Location: /install.php");
            exit();
        });

        // $_router->before('GET|POST', '/admin/.*', function () {
        //     if (!isset($_SESSION['user'])) {
        //         header('location: /auth/login');
        //         exit();
        //     }
        // });

        $_router->mount('/admin', function () use ($_router) {
            foreach (PANDA_ADMIN_GET_ROUTES as $admin_get_route) {
                [$route, $controller, $func] = $admin_get_route;
                $_router->get($route, "$controller@$func");
            }

            foreach (PANDA_ADMIN_POST_ROUTES as $admin_post_route) {
                [$route, $controller, $func] = $admin_post_route;
                $_router->post($route, "$controller@$func");
            }
        });
    }


    public function run()
    {
        $this->router->run();
    }
}
