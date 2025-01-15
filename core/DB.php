<?php

namespace Panda;

use MongoDB\Client as MongoDBClient;
use MongoDB\Database;
use Exception;
use Panda\Services\Logger as PandaLoggerService;

class DB {

    private static self|null $instance = null;
    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;
    public readonly PandaLoggerService $logger;


    final private function __construct() {
        $this->logger = Logger::getInstance()->logger;
        $this->initializeMongoDB();
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

    private function initializeMongoDB(): void {
        try {
            // !TODO if MONGO_URI starts with "mongodb://", no need to use tlsCAFile and tls=true
            // ! Like this: $this->mongo_client = new MongoDBClient(env("MONGO_URI"));
            $this->mongo_client = new MongoDBClient(env("MONGO_URI"), [
                'tls' => true,
                'tlsCAFile' => PANDA_ROOT . env("MONGO_TLS_CA_FILE"),
            ]);
            $this->db = $this->mongo_client->selectDatabase(env("DATABASE_NAME"));

            $this->mongo_client->selectDatabase('admin')->command(['ping' => 1]);

            // create collections if not already created
            $collections = iterator_to_array($this->db->listCollectionNames());

            // ! this is for update actually. Let's say, a new version needs a new collection, what then?
            // ! the installation step is no more applicable. 
            // ! then, we need this step to make sure the new collection will be created.
            foreach (MONGO_DEFAULT_COLLECTIONS as $collection) {
                if (!in_array($collection, $collections)) {
                    $this->logger->warning("Missing table: $collection");
                    $this->db->createCollection($collection);
                }
            }
        } catch (Exception $error) {
            $this->logger->error("Failed to connect to MongoDB: " . $error->getMessage());
            if (env('APP_ENV') == 'development') {
                var_dump($error);
            }
            exit(1);
        }
    }
}
