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
        $this->router->get('/', '\Panda\Controller\HomeController@index');
        $this->router->get('/about', '\Panda\Controller\HomeController@about');
        $this->router->get("/install", function () {
            header("Location: /install.php");
        });

        $admin_router = $this->router;
        $admin_router->mount('/admin', function () use ($admin_router) {
            $admin_router->get('/', '\Panda\Admin\Controller\HomeController@index');
        });
    }


    public function run()
    {
        $this->router->run();
    }
}
