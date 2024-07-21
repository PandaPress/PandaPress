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

        $_router->get('/', '\Panda\Controllers\HomeController@index');
        $_router->get('/about', '\Panda\Controllers\HomeController@about');

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
            $_router->get('/', '\Panda\Admin\Controllers\HomeController@index');
            $_router->get('/compose', '\Panda\Admin\Controllers\PostController@compose');
            $_router->post('/save', '\Panda\Admin\Controllers\PostController@save');
        });
    }


    public function run()
    {
        $this->router->run();
    }
}
