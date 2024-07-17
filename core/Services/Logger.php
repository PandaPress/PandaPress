<?php

namespace Panda\Services;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    public function log($level, string|\Stringable $message, array $context = []) : void {
        // TODO: Implement log() method.
        echo $message;
    }
}
