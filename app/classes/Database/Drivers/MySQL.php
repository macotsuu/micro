<?php
    namespace Database\Drivers;

    use PDO;
    use PDOException;
    
    class MySQL 
    {
        private PDO $pdo;
        
        public function __construct(
            $host = DATABASE_HOST,
            $user = DATABASE_USER,
            $pass = DATABASE_PASSWORD,
            $db = DATABASE_NAME
        ) {
            try {
                $dsn = $this->getDSN($host, $db);

                $this->pdo = new PDO($dsn, $user, $pass);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            } catch (PDOException $ex) {
                print_r($ex->getMessage());
            }
        }
        
        public function getConnection(): PDO {
            return $this->pdo;
        }
        
        public function getDSN(string $host, string $dbname): string {
            return "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        }
    }
