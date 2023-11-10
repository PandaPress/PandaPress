<?php


namespace Panda;

use Medoo\Medoo;

class PandaDB
{
    private static self|null $instance = null;

    public  Medoo|null $db = null;

    final private function __construct()
    {
        $this->db = new Medoo([
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

    public static function getInstance(): PandaDB
    {
        if (PandaDB::$instance === null) {
            PandaDB::$instance = new PandaDB;
        }

        return PandaDB::$instance;
    }
}
