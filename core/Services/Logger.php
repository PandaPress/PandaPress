<?php

namespace Panda\Services;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    private string $log_file;

    function __construct(string $log_file)
    {
        $this->log_file = $log_file;
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        error_log($level . ':' . $message . "\n\n", 3, $this->log_file);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        error_log("Error : " . $message . "\n\n", 3, $this->log_file);
    }
}
