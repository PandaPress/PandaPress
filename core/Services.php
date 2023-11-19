<?php


namespace Panda;

use Panda\Services\PandaBase;
use Panda\Services\Logger;

class Services
{
    private static self|null $instance = null;

    public readonly PandaBase $db;

    public readonly Logger $logger;

    final private function __construct()
    {
        $this->db = new PandaBase([
            "type" => $_ENV['DB_TYPE'] ?? 'mysql',
            "host" => $_ENV['DB_HOST'],
            "database" => $_ENV['DB_NAME'],
            "username" => $_ENV['DB_USERNAME'],
            "password" => $_ENV['DB_PASSWORD']
        ]);
    }
    final public function __clone()
    {
    }
    final public function __wakeup()
    {
    }

    public static function init(): self
    {
        self::$instance ??= new self;
        return self::$instance;
    }
}
