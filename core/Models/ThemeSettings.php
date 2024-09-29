<?php

namespace Panda\Models;

use Panda\Utils\SettingStorageType;


class ThemeSettings extends AbstractSettings {
    private SettingStorageType $storage_type;
    private string $theme_dir;


    public function __construct(SettingStorageType $storage_type, string $theme_dir) {

        if (!in_array($storage_type, [SettingStorageType::FILE, SettingStorageType::DATABASE])) {
            throw new \Exception("Invalid storage type");
        }

        $this->storage_type = $storage_type;
        $this->theme_dir = $theme_dir;
    }

    public function getSettings(): array {
        $defaultSettings = $this->getDefaultSettings();
        $customSettings = $this->getCustomSettings();

        return array_merge($defaultSettings, $customSettings);
    }

    private function getDefaultSettings(): array {
        return PANDA_THEME_SETTINGS;
    }
    private function getCustomSettings(): array {
        if ($this->storage_type === SettingStorageType::FILE) {
            $customSettingsPath = $this->theme_dir . '/settings.json';

            if (!file_exists($customSettingsPath)) {
                return [];
            }

            $customSettingsJson = file_get_contents($customSettingsPath);
            return json_decode($customSettingsJson, true);
        }

        if ($this->storage_type === SettingStorageType::DATABASE) {
            // TODO: implement database storage
            return [];
        }

        return [];
    }

    public function updateCustomSettings(array $newSettings) {
        if ($this->storage_type === SettingStorageType::FILE) {
            $customSettingsPath = $this->theme_dir . '/settings.json';

            $existingSettings = $this->getCustomSettings();
            $updatedSettings = array_merge($existingSettings, $newSettings);

            $fp = fopen($customSettingsPath, 'w');

            if (flock($fp, LOCK_EX)) {
                ftruncate($fp, 0);
                fwrite($fp, json_encode($updatedSettings, JSON_PRETTY_PRINT));
                flock($fp, LOCK_UN);
            } else {
                throw new \Exception("Could not obtain lock on custom settings file");
            }

            fclose($fp);
        }

        if ($this->storage_type === SettingStorageType::DATABASE) {
            // TODO: implement database storage
        }
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
