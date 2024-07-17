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
        $this->theme_dir = root() . "/ext/themes/" . $_ENV['CURRENT_THEME'];
        $this->latte->setTempDirectory(root() . "/cache/templates");
    }
    public function index()
    {
        return  $this->latte->render($this->theme_dir . "/index.latte", ['name' => 'Panda']);
    }


    public function about()
    {
        global $pandadb;
        $theme = $pandadb->select("panda_options", ["option_key", "option_value"], [
            "option_key" => 'theme'
        ]);

        echo json_encode([
            "theme" => $theme
        ]);

        // echo json_encode($_ENV);
    }
}
