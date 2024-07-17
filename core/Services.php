<?php


namespace Panda;

// use Panda\Services\PandaBase;
use Panda\Services\Logger;
use MongoDB\Client as MongoDBClient;
use MongoDB\Database;


class Services
{
    private static self|null $instance = null;

    public readonly MongoDBClient $mongo_client;
    public readonly Database $db;

    public readonly Logger $logger;

    final private function __construct()
    {
        // $this->db = new PandaBase([
        //     "type" => $_ENV['DB_TYPE'] ?? 'mysql',
        //     "host" => $_ENV['DB_HOST'],
        //     "database" => $_ENV['DB_NAME'],
        //     "username" => $_ENV['DB_USERNAME'],
        //     "password" => $_ENV['DB_PASSWORD'],
        //     "port" => $_ENV['DB_PORT'] ?? 3306
        // ]);

   
        $this->mongo_client = new MongoDBClient(
            $_ENV['MONGO_URI'], 
            [
                'tls' => true,
                'tlsCAFile' => root() . "/ssl/isrgrootx1.pem"
            ]
        );

        $this->mongo_client->selectDatabase('admin')->command(['ping' => 1]);
        echo "Pinged your deployment. You successfully connected to MongoDB!\n";

        $this->db = $this->mongo_client->selectDatabase("pandacms");
        

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
