<?php

namespace Panda\Controllers;


use Latte\Engine;
use Latte\Essential\RawPhpExtension;
use Panda\Models\ThemeSettings;


class BaseController {

    protected $template_engine;
    protected $current_theme;
    protected $current_theme_dir;
    protected $current_theme_views;

    private $user_data;
    private $user_id;


    protected $theme_settings;

    public function __construct() {
        // template engine
        $this->template_engine = new Engine();

        // Get theme from MongoDB settings, fallback to "papermod"

        $theme_settings = (new ThemeSettings())->getSettings();

        $current_theme_id = $theme_settings['current_theme'];

        if ($current_theme_id) {
            // read theme info from panda.json
            $theme_info = json_decode(file_get_contents(PANDA_THEMES . "/$current_theme_id/panda.json"), true);
            $current_theme_dir = $theme_info['id'];
            $this->current_theme = $current_theme_id;
            $this->current_theme_dir = PANDA_THEMES . "/$current_theme_dir";
            $this->current_theme_views = PANDA_THEMES . "/$current_theme_dir/Views";
        } else {
            $this->current_theme = "papermod";
            $this->current_theme_dir = PANDA_THEMES . "/papermod";
            $this->current_theme_views = PANDA_THEMES . "/papermod/Views";
        }



        // set template engine template directory
        $cache_panda_tmpl_dir = PANDA_ROOT . "/cache/templates/$this->current_theme";
        if (!is_dir($cache_panda_tmpl_dir)) {
            mkdir($cache_panda_tmpl_dir, 0755, true);
        }
        $this->template_engine->setTempDirectory($cache_panda_tmpl_dir);
        $this->template_engine->addExtension(new RawPhpExtension);

        $this->user_data = \Panda\Session::get('user_data');
        $this->user_id = \Panda\Session::get('user_id');

        $this->theme_settings = new ThemeSettings($this->current_theme_dir, $this->current_theme);
    }

    public function json($data, $status_code = 200) {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }

    protected function appendMetaData(array $params = []) {
        return array_merge($params, [
            "theme_settings" => $this->theme_settings->getSettings(),
            "user_data" => $this->user_data,
            "user_id" => $this->user_id
        ]);
    }
}
