<?php

namespace Panda\Models;

use Panda\Interfaces\IThemeSettings;

class ThemeSettings implements IThemeSettings {

    private string $theme_id;
    private array $custom_settings;
    public function __construct() {
        global $pandadb;
        $settings = $pandadb
            ->selectCollection('settings')
            ->findOne(['current_theme' => $this->theme_id]);

        $this->theme_id =  $settings ? $settings['current_theme'] : 'papermod';
        $this->custom_settings = $settings['settings'] ?? [];
    }

    public function getCurrentThemeId(): string {
        return $this->theme_id;
    }

    public function getSettings(): array {
        $defaultSettings = $this->getDefaultSettings();
        return array_merge($defaultSettings, $this->custom_settings);
    }

    // alway get default settings from settings.php file in theme directory
    private function getDefaultSettings(): array {
        $settings_file =  PANDA_THEMES . "/" . $this->theme_id . "/settings.php";
        if (!file_exists($settings_file)) {
            throw new \RuntimeException("Default Settings file not found: " . $settings_file);
        }
        $default_settings = require_once $settings_file;
        return $default_settings;
    }
    public function getCustomSettings(): array {
        return $this->custom_settings;
    }

    public function updateCustomSettings(array $newSettings) {
        global $pandadb;
        $pandadb
            ->selectCollection('settings')
            ->updateOne(
                [
                    'current_theme' => $this->theme_id
                ],
                [
                    '$set' => array_combine(
                        array_map(
                            fn($key) => "settings.$key",
                            array_keys($newSettings)
                        ),
                        array_values($newSettings)
                    )
                ],
                ['upsert' => true]
            );
    }

    // ! this is for migration, export settings to json file
    public function exportCustomSettings(): bool|string {
        return json_encode($this->getCustomSettings(), JSON_PRETTY_PRINT);
    }

    // ! this is for migration, import settings from json file
    public function importCustomSettings(string $json_file_path): string {
        try {
            $json_content = file_get_contents($json_file_path);
            if ($json_content === false) {
                throw new \RuntimeException("Failed to read the JSON file.");
            }

            $settings = json_decode($json_content, associative: true, flags: JSON_THROW_ON_ERROR);

            if (!is_array($settings)) {
                throw new \UnexpectedValueException("Imported settings must be an array.");
            }

            $this->updateCustomSettings($settings);

            $newSettings = $this->getSettings();

            return json_encode([
                "code" => 0,
                "success" => true,
                "data" => $newSettings,
                'message' => "Settings imported successfully"
            ]);
        } catch (\Throwable $e) {
            return json_encode([
                "code" => -1,
                "success" => false,
                "data" => null,
                'message' => "Error importing settings: " . $e->getMessage()
            ]);
        }
    }
}
