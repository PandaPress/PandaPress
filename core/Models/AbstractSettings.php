<?php

namespace Panda\Models;

abstract class AbstractSettings {
    abstract public function getSettings(): array;
    abstract public function updateCustomSettings(array $settings);

    abstract public function export(): bool|string;
    abstract public function import(string $json_file_path): string;
}
