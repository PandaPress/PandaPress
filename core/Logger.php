<?php

namespace Panda;

use Panda\Services\Logger as PandaLoggerService;


class Logger {

    private static self|null $instance = null;

    public readonly PandaLoggerService $logger;

    final private function __construct() {
        $this->initializeLogger();
    }

    public static function getInstance(): self {
        self::$instance ??= new self;
        return self::$instance;
    }


    final public function __clone() {
        throw new \RuntimeException('Cloning is not allowed.');
    }
    final public function __wakeup() {
        throw new \RuntimeException('Unserialize is not allowed.');
    }

    private function initializeLogger(): void {
        $log_path =   PANDA_ROOT . "/logs";
        $log_file = $log_path . "/pandapress.log";

        if (!is_writable($log_path)) {
            chmod($log_path, 0755);
        }

        if (!file_exists($log_file)) {
            touch($log_file);
        }

        $this->logger = new PandaLoggerService($log_file);
    }
}
