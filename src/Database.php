<?php
    namespace Micro;

    use PDO;
    use PDOException;

    class Database {
        private static null|Database $instance = null;
        private PDO $pdo;

        public static function getInstance(): Database {
            if (is_null(self::$instance)) {
                $dsn = sprintf('mysql:host=%s;port=3307;dbname=%s;', config()->get('database.host'), config()->get('database.name'));

                try {
                    $pdo = new PDO($dsn, config()->get('database.username'), config()->get('database.password'));
                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

                    self::$instance = new Database();
                    self::$instance->pdo = $pdo;
                } catch (PDOException $PDOException) {
                    logger()->log('errors/Database', print_r($PDOException->getMessage(), true));
                }
            }

            return self::$instance;
        }

        public function execute(string $query, array $params = []): int {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            return $stmt->rowCount();
        }

        public function get(string $query, array $params = []): array {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll();
        }
    }