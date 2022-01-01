<?php

namespace Database;

use Database\Drivers\MySQL;
use Exception;
use PDO;
use PDOException;

class Connection
{
    private PDO $pdo;
    private array $drivers = [
        'mysql' => MySQL::class
    ];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!isset($this->drivers[DATABASE_DRIVER])) {
            throw new Exception("Drivers not supported.");
        }

        $driver = $this->drivers[DATABASE_DRIVER];
        $instance = new $driver;

        $this->pdo = $instance->getConnection();
    }

    /**
     * @throws Exception
     */
    public function get(string $query, array $parameters = []): array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($parameters);
        } catch (PDOException $ex) {
            if (APP_ENV === 'local') {
                throw new Exception($ex->getMessage());
            }

            throw new Exception("Problem with Query");
        }

        if ($result === false) {
            return [];
        }

        return $stmt->fetchAll();
    }

    /**
     *
     * @param string $query
     * @param array $parameters
     *
     * @return int|false
     *
     */
    public function execute(string $query, array $parameters = []): int|bool
    {
        $stmt = $this->pdo->prepare($query);

        if ($stmt->execute($parameters)) {
            return $stmt->rowCount();
        }

        return false;
    }
}
