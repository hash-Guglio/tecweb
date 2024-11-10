<?php
    require_once("ini.php");

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    class Database {
        private $connection;
        private const ERR_DUPLICATE = 1062;
        private const ERR = "Error in database.php";
    
        public function __construct() {
		        try {
			      $this->connection = new mysqli(
				        getenv('DB_HOST'),
				        getenv('DB_USER'),
				        getenv('DB_PASS'),
				        getenv('DB_NAME')
			      );
			      $this->connection->set_charset("utf8mb4");
			      $this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
		        } catch  (Exception $e) {
			          error_log($e->getMessage());
			          throw new Exception(self::ERR);
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
                throw new Exception(self::ERR_DEFAULT);
            }

            if (!empty($params)) {
                $types = $types ?: str_repeat("s", count($params));
                if (!$stmt->bind_param($types, ...$params)) {
                    throw new Exception("Parameter binding error.");
                }
            }

            if (!$stmt->execute()) {
                throw new Exception("Query execution error.");
            }

            return $stmt;
        }

        private function executeSelectQuery(string $query, array $params = [], string $types = ""): ?array {
            try {
                $stmt = $this->executePreparedQuery($query, $params, $types);
                $result = $stmt->get_result();
                $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : null;

                $result?->close();
                $stmt->close();
                return $data;
            } catch (mysqli_sql_exception $e) {
                $this->handleDatabaseException($e);
            }
            return null;
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
            throw new Exception(self::ERR_DEFAULT);
        }

    }

    // ==========================
    // User-related Queries
    // ==========================
    
    public function authenticateUser($username, $password) : array {
        $query = "SELECT id, is_admin, password FROM utente WHERE username = ?";
        $params = [$username];
    
        $res = $this->executeSelectQuery($query, $params);
        
        if (!empty($res) && password_verify($password, $res[0]["password"])) {

        return [
            "id" => $res[0]["id"],
            "is_admin" => $res[0]["is_admin"]
        ];
    }

    }
 
    public function getUsernameByUserId($id): array {
        $query = "SELECT username FROM users WHERE id = ?";
        $params = [$id];
        $types = "i";

        $result = $this->executeSelectQuery($query, $params, $types);

        return $result;
    }
?>
