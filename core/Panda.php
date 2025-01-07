<?php


namespace Panda;

// use Panda\Services\PandaBase;
use Panda\Services\Logger;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;
use Exception;
use Panda\Router;


class Panda {
    private static self|null $instance = null;

    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;

    public readonly Logger $logger;

    public readonly Router $router;

    final private function __construct() {
        // initialize router
        $this->router = new Router();

        // initialize logger
        $this->initializeLogger();

        // initialize MongoDB 
        $this->initializeMongoDB();
    }

    final public function __clone() {
    }
    final public function __wakeup() {
    }

    public static function getInstance(): self {
        self::$instance ??= new self;
        return self::$instance;
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

        $this->logger = new Logger($log_file);
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

    public function start(): void {
        $this->router->run();
    }
}
