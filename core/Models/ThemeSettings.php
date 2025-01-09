<?php

namespace Panda\Models;



class ThemeSettings extends AbstractSettings {
    private string $theme_dir;

    private string $theme_name;


    public function __construct(string $theme_dir, string $theme_name) {

        $this->theme_dir = $theme_dir;
        $this->theme_name = $theme_name;
    }

    public function getSettings(): array {
        $defaultSettings = $this->getDefaultSettings();
        $customSettings = $this->getCustomSettings();
        return array_merge($defaultSettings, $customSettings);
    }

    private function getDefaultSettings(): array {
        return PANDA_THEME_SETTINGS;
    }
    public function getCustomSettings(): array {
        global $pandadb;
        $settings = $pandadb->selectCollection("settings")->findOne(["theme_name" => $this->theme_name]);
        return $settings ?? [];
    }

    public function updateCustomSettings(array $newSettings) {
        global $pandadb;
        $pandadb->selectCollection('settings')->updateOne(['theme_name' => $this->theme_name], ['$set' => $newSettings], ['upsert' => true]);
    }

    // ! this is for migration, export settings to json file
    public function export(): bool|string {
        return json_encode($this->getSettings(), JSON_PRETTY_PRINT);
    }

    // ! this is for migration, import settings from json file
    public function import(string $json_file_path): string {
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
