<?php


namespace Panda;

// use Panda\Services\PandaBase;
use Panda\Services\Logger;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;
use Exception;
use Panda\Router;

class Panda
{
    private static self|null $instance = null;

    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;

    public readonly Logger $logger;

    private Router $router;

    final private function __construct()
    {
        $this->initialize();
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

    private function initialize(): void
    {

        // initialize router
        $this->router = new Router();

        // initialize logger
        $log_path =   root() . "/logs";
        $log_file = $log_path . "/pandacms.log";

        if (!is_writable($log_path)) {
            chmod($log_path, 0755);
        }

        if (!file_exists($log_file)) {
            touch($log_file);
        }

        $this->logger = new Logger($log_file);

        // initialize MongoDB 
        $this->mongo_client = new MongoDBClient(getenv('MONGO_URI'), [
            'tls' => true,
            'tlsCAFile' => getenv('MONGO_TLS_CA_FILE'),
            // 'tlsAllowInvalidCertificates' => true,
            // 'tlsAllowInvalidHostnames' => true

        ]);
        $this->db = $this->mongo_client->selectDatabase("pandacms");
        try {
            $this->mongo_client->selectDatabase('admin')->command(['ping' => 1]);
        } catch (Exception $error) {
            $this->logger->error("Failed to connect to MongoDB: " . $error->getMessage());
            var_dump($error);
            exit(1);
        }
    }

    public function start(): void
    {
        $this->router->run();
    }
}
