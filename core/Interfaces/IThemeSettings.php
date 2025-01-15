<?php

namespace Panda\Interfaces;

interface IThemeSettings {
    public function getSettings(): array;

    public function getCustomSettings(): array;
    public function updateCustomSettings(array $settings);

    public function exportCustomSettings(): bool|string;
    public function importCustomSettings(string $json_file_path): string;
}
