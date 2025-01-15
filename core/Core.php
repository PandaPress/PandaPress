<?php


namespace Panda;

// use Panda\Services\PandaBase;
use Panda\Services\Logger as PandaLoggerService;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;

use Panda\Router;


class Core {
    private static self|null $instance = null;

    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;

    public readonly PandaLoggerService $logger;

    public readonly Router $router;

    final private function __construct() {
        // initialize router
        $this->db = DB::getInstance()->db;
        $this->logger = Logger::getInstance()->logger;
        $this->router = new Router();
    }

    final public function __clone() {
        throw new \RuntimeException('Cloning is not allowed.');
    }
    final public function __wakeup() {
        throw new \RuntimeException('Unserialize is not allowed.');
    }

    public static function getInstance(): self {
        self::$instance ??= new self();
        return self::$instance;
    }

    public function start(): void {
        $this->router->run();
    }
}
