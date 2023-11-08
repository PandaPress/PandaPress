<?php

namespace Panda;

use Bramus\Router as BramusRouter;

class Router
{
    private $router;


    public function __construct()
    {
        $this->router = new BramusRouter();
        $this->router->setNamespace("\Panda\Controller");
        $this->setRoutes();
    }

    public function setRoutes()
    {
        $this->router->get('/', 'HomeController@index');
        $this->router->get('/about', 'HomeController@about');
    }


    public function run()
    {
        $this->router->run();
    }
}
