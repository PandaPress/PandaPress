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
        });
    }


    public function run()
    {
        $this->router->run();
    }
}
