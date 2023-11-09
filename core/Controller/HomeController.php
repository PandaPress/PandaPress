<?php

namespace Panda\Controller;


use Latte\Engine;

class HomeController
{
    private $latte;
    private $theme_dir;


    public function __construct()
    {
        $this->latte = new Engine();
        $this->theme_dir = root() . "/ext/themes/bear";
        $this->latte->setTempDirectory(root() . "/tmp");
    }
    public function index()
    {
        return  $this->latte->render($this->theme_dir . "/index.latte", ['name' => 'Panda']);
    }


    public function about()
    {
        echo "home about page. Test:  " . $_ENV['Test'];
    }
}
