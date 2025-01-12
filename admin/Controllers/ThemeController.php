<?php

namespace Panda\Admin\Controllers;

use Throwable;

class ThemeController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    private function getAllThemeData() {
        $themeDirs = array_diff(scandir(PANDA_THEMES), array('.', '..'));
        $themes = [];

        foreach ($themeDirs as $themeDir) {
            $jsonPath = PANDA_THEMES . '/' . $themeDir . '/panda.json';
            if (file_exists($jsonPath)) {
                try {
                    $jsonContent = file_get_contents($jsonPath);
                    if ($jsonContent === false) {
                        global $logger;
                        $logger->error("Could not read file: $jsonPath");
                        continue;
                    }

                    $themeData = json_decode($jsonContent, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        global $logger;
                        $logger->error("Invalid  JSON in file: $jsonPath - " . json_last_error_msg());
                        continue;
                    }



                    $themes[] = $themeData;
                } catch (Throwable $e) {
                    global $logger;
                    $logger->error("Error processing theme file $jsonPath: " . $e->getMessage());
                }
            }
        }

        return $themes;
    }

    private function getCurrentTheme(): string {
        global $pandadb;
        $settings = $pandadb->selectCollection('settings')->findOne(['current_theme' => ['$exists' => true]]);
        return $settings['current_theme'] ?? 'papermod';
    }

    public function index() {
        $themes = $this->getAllThemeData();
        $currentTheme = $this->getCurrentTheme();

        return $this->template_engine->render("$this->views/themes/index.latte", $this->appendUserData([
            'themes' => $themes,
            'current_theme' => $currentTheme
        ]));
    }



    public function settings() {
        $themes = $this->getAllThemeData();
        $currentTheme = $this->getCurrentTheme();

        return $this->template_engine->render("$this->views/themes/settings.latte", $this->appendUserData([
            'current_theme' => $currentTheme,
            'themes' => $themes
        ]));
    }

    public function setCurrentThemeId() {
        $newThemeId = $_POST['theme'] ?? null;
        if (!$newThemeId) {
            return json_encode(['error' => 'No theme specified']);
        }

        try {
            global $pandadb;
            $pandadb->selectCollection('settings')->updateOne(
                ['current_theme' => ['$exists' => true]],
                ['$set' => ['current_theme' => $newThemeId]],
                ['upsert' => true]
            );

            return json_encode(['success' => true]);
        } catch (Throwable $e) {
            global $logger;
            $logger->error($e->getMessage());
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
