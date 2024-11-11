<?php
    require_once('ini.php');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    class Database {
        private $connection;
        private const ERR_DUPLICATE = 1062;
        private const ERR_CONNECTION_FAILED = "Connection to the database failed.";
        private const ERR_PREPARE_STATEMENT = "Failed to prepare the SQL statement.";
        private const ERR_EXECUTE_STATEMENT = "Failed to execute the SQL statement.";
        private const ERR_INVALID_PARAMETERS = "Invalid parameters provided for the statement.";

        public function __construct() {
		        try {
			      $this->connection = new mysqli(
				        'mariadb',
				        getenv('MARIADB_USER'),
				        getenv('MARIADB_PASSWORD'),
				        getenv('MARIADB_DATABASE')
            );
			      $this->connection->set_charset("utf8mb4");
			      $this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
            } catch (Exception $e) {
			          throw new Exception(self::ERR_CONNECTION_FAILED);
		        }
        }

        public function __destruct() {
		        if ($this->connection) {
			      $this->connection->close();
            }
        }

        private function sanitizeInput(&$data) {
            if (is_array($data)) {
                array_walk_recursive($data, function (&$item) {
                    if (is_string($item)) {
                        $item = strip_tags(trim(html_entity_decode($item, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5)));
                    }
                });
            } elseif (is_string($data)) {
                $data = strip_tags(trim(html_entity_decode($data, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5)));
            }
        }

        private function executePreparedQuery(string $query, array $params = [], string $types = ""): mysqli_stmt {
            $this->sanitizeInput($params);
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
                throw new Exception(self::ERR_PREPARE_STATEMENT);
            }

            if (!empty($params)) {
                $types = $types ?: str_repeat("s", count($params));
                if (!$stmt->bind_param($types, ...$params)) {
                    throw new Exception(self::ERR_INVALID_PARAMETERS);
                }
            }

            if (!$stmt->execute()) {
                throw new Exception(self::ERR_EXECUTE_STATEMENT);
            }
            return $stmt;
        }

        private function executeSelectQuery(string $query, array $params = [], string $types = ""): ?array {
            $stmt = $this->executePreparedQuery($query, $params, $types);

            $result = $stmt->get_result();

            $data = $result->fetch_all(MYSQLI_ASSOC);
            $result->close();
			      $stmt->close();
            
            return $data;
        }

        private function executeUpdateQuery(string $query, array $params = [], string $types = ""): bool {
            try {
                $stmt = $this->executePreparedQuery($query, $params, $types);
                $affectedRows = $stmt->affected_rows;
                $stmt->close();
                return $affectedRows > 0;
            } catch (mysqli_sql_exception $e) {
                return $this->handleDatabaseException($e);
            }
        }

        private function executeInsertMultiple(string $query, array $paramSets, string $types = ""): bool {
            try {
                $stmt = null;
                $totalAffectedRows = 0;
                foreach ($paramSets as $params) {
                    $stmt = $this->executePreparedQuery($query, $params, $types, $stmt);
                    $totalAffectedRows += $stmt->affected_rows;
                }   

                $stmt?->close();
                return $totalAffectedRows > 0;
            } catch (mysqli_sql_exception $e) {
                return $this->handleDatabaseException($e);
            }
        }

        private function handleDatabaseException(mysqli_sql_exception $e): bool {
            if ($e->getCode() == self::ERR_DUPLICATE) {
                return false;
            }
            throw new Exception($e->getMessage());
        }


        // ==========================
        // User-related Queries
        // ==========================
    
        public function authenticateUser($username, $password) : array {
            $query = "SELECT id, usr_is_admin, usr_password FROM user WHERE usr_name = ?";
            $params = [$username];
            $res = $this->executeSelectQuery($query, $params);

            if (!empty($res) && password_verify($password, $res[0]["usr_password"])) {
                return [
                    "id" => $res[0]["id"],
                    "is_admin" => $res[0]["usr_is_admin"]
                ];
            };
            return [];
        }
 
        public function getUsernameByUserId($id): array {
            $query = "SELECT usr_name FROM user WHERE id = ?";
            $params = [$id];
            $types = "i";

            $result = $this->executeSelectQuery($query, $params, $types);

            return $result;
        }

    }
?>
