<?php


namespace Panda;

// use Panda\Services\PandaBase;
use Panda\Services\Logger;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;
use Exception;


class Services
{
    private static self|null $instance = null;

    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;

    public readonly Logger $logger;

    final private function __construct()
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->mongo_client = new MongoDBClient(
            $_ENV['MONGO_URI'],
            [
                'tls' => true,
                'tlsCAFile' => root() . "/ssl/" . $_ENV['MONGO_SSL_FILE']
            ]
        );
        $this->db = $this->mongo_client->selectDatabase("pandacms");

        try {
            $this->mongo_client->selectDatabase('admin')->command(['ping' => 1]);
            throw new Exception("sss");
        } catch (Exception $error) {

            $log_path =   root() . "/logs";
            $log_file = $log_path . "/pandacms.log";

            if (!is_writable($log_path)) {
                chmod($log_path, 0755);
            }

            if (!file_exists($log_file)) {
                touch($log_file);
            }

            $this->logger = new Logger($log_file);
            $this->logger->error("Failed to connect to MongoDB: " . $error->getMessage());

            echo $error->getMessage();
            exit(1);
        }
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
