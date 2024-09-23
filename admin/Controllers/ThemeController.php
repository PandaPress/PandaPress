<?php

namespace Panda\Admin\Controllers;

class ThemeController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->template_engine->render("$this->views/themes/index.latte");
    }

    public function settings() {
        return $this->template_engine->render("$this->views/themes/settings.latte", $this->appendUserData([
            'current_theme' => env("CURRENT_THEME"),
            'themes' =>  array_diff(scandir(PANDA_THEMES), array('.', '..'))
        ]));
    }

    public function current() {
        $newTheme = $_POST['theme'] ?? null;
        if (!$newTheme) {
            return json_encode(['error' => 'No theme specified']);
        }

        $envFile = PANDA_ROOT . '/.env';
        $envContents = file_get_contents($envFile);
        $updatedContents = preg_replace(
            '/^CURRENT_THEME=.*/m',
            "CURRENT_THEME=$newTheme",
            $envContents
        );

        $success = file_put_contents($envFile, $updatedContents);

        $return = ['success' => true];

        if (!$success) {
            $return['success'] = false;
            $return['message'] = 'Failed to update .env file';
        }

        return json_encode($return);
    }
}
