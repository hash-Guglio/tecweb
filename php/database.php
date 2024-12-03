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
            } catch (mysqli_sql_exception $e) {
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

        private function composeQueryAndParameters(
            string &$query,
            array &$params,
            string &$types,
            array $fields,
            bool $isUpdate
            ): void {
                
            $setClauses = [];
            $placeholders = [];
            
            foreach ($fields as $field) {
                [$value, $column, $type] = $field;

                if ($value != null) {
                    if ($isUpdate) {
                        $setClauses[] = "$column = ?";
                    } else {
                        $placeholders[] = "?";
                    }
                    
                    $params[] = $value;
                    $types .= $type;
                }
            }

            if ($isUpdate) {
                $query .= implode(", ", $setClauses);
            } else {
                $query .= implode(", ", array_column($fields, 1)) . ") VALUES (" . implode(", ", $placeholders) . ")";
            }
        }

        private function handleDatabaseException(mysqli_sql_exception $e): bool {
            if ($e->getCode() == self::ERR_DUPLICATE) {
                return false;
            }
            throw new Exception($e->getMessage());
        }

        // ==========================
        // Component creation realted Queries
        // ==========================

        public function getSchemaSelect($searchType, $filterName) {

            $filters = [];

            switch ($searchType) {
                case "ricette":
                    switch ($filterName) {
                        case "dish_type":    
                            $query = "SELECT id, dt_type FROM dish_type;";
                            break;
                        case "allgs":
                            $query = "SELECT id, restriction_type, disorder_name FROM restriction;";
                            break;
                        default:
                            $query = "";
                            break;
                    }
                case "ingrediente":
                    break;
                default:
                    $query = "SELECT SUBSTRING(COLUMN_TYPE, 6, LENGTH(COLUMN_TYPE) - 6) AS genders FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME = 'usr_gender';";
                    break;
            }

            $res = $this->executeSelectQuery($query);
            if ($searchType === "utente") {
                $res = str_replace("'", '', $res[0]['genders']);
                return explode(',', $res);
            }

            return $res; 
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

        public function getUserDataByUserId($id): array {
            $query = "SELECT * FROM user WHERE id = ?";
            $params = [$id];
            $types = "i";

            $result = $this->executeSelectQuery($query, $params, $types);

            return $result;
        }

        
        // ==========================
        // Insert(without param id) Update(with param id) User
        // ==========================
    
        public function persistUser(
            string $usr_name,
            string $usr_mail,
            string $usr_first_name,
            string $usr_birth_date,
            string $usr_gender = 'altro',
            ?string $usr_new_password = null,
            bool $usr_is_vegan = false,
            bool $usr_is_celiac = false,
            bool $usr_is_lint = false,
            ?int $id = null
            ): array {
    
            $isUpdate = !is_null($id);

            if ($isUpdate) {
                $query = "UPDATE user SET ";
            } else {
                $query = "INSERT INTO user (";
            }

            $params = [];
            $types = "";

            if ($usr_new_password != null) {
                $usr_new_password = password_hash($usr_new_password, PASSWORD_DEFAULT);
            }

            $fields = [
                [$usr_name, "usr_name", "s"],
                [$usr_mail, "usr_mail", "s"],
                [$usr_first_name, "usr_first_name", "s"],
                [$usr_gender, "usr_gender", "s"],
                [$usr_birth_date, "usr_birth_date", "s"],
                [$usr_new_password, "usr_password", "s"]
            ];

            $this->composeQueryAndParameters($query, $params, $types, $fields, $isUpdate);

            if ($isUpdate) {
                $query .= " WHERE id = ?";
                $params[] = $id;
                $types .= "i";
            }

            $result = $this->executeUpdateQuery($query, $params, $types);

            return [
                $result,
                $isUpdate ? $id : $this->connection->insert_id
            ];
        }

        
        // ==========================
        // Recipe-related Queries
        // ==========================

        public function searchRecipe($str, $limit, $offest) : array {
            $base = "FROM recipe WHERE rcp_title LIKE ? ORDER BY rcp_title";

            $res = [];

            $query = "SELECT id, rcp_title AS name, rcp_image AS image, rcp_ready_minutes AS ready_in, rcp_servings AS servings " . $base . " LIMIT ? OFFSET ?";

            $params = ["%". trim($str) . "%", $limit, $offest]; 
            $types = "sii";
            
            $res['recipe'] = $this->executeSelectQuery($query, $params, $types);

            $query = "SELECT COUNT(*) AS total " . $base;
            $params = ["%". trim($str) . "%"];
            $types = "s";
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
        
            return $res;
        }

        public function searchRecipeByType($str, $limit, $offest, $dish_type) : array {
            $base = "FROM recipe AS r JOIN dish_type_recipe AS dtr ON r.id = dtr.recipe JOIN dish_type AS dt ON dtr.dish_type = dt.id  WHERE r.rcp_title  LIKE ? AND dt.id = ? ORDER BY r.rcp_title ASC";

            $res = [];

            $query = "SELECT r.id, r.rcp_title AS name, r.rcp_image AS image, r.rcp_ready_minutes AS ready_in, r.rcp_servings AS servings " . $base . " LIMIT ? OFFSET ?";

            $params = ["%". trim($str) . "%", $dish_type, $limit, $offest];
            $types = "siii";
            $res['recipe'] = $this->executeSelectQuery($query, $params, $types);
 
            $query = "SELECT COUNT(*) AS total " . $base;
            $params = ["%". trim($str) . "%", $dish_type];
            $types = "si";
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
        
            return $res;
        }

        public function searchRecipeByAllgs($str, $limit, $offset, $allgs) : array {
            $base = "FROM recipe AS r WHERE r.rcp_title LIKE ?";

            $params = ["%". trim($str) . "%"];
            $res = [];
            $types = "s";

            if (isset($allgs["vegan"])) {
                $base .= "AND rcp_is_vegan = ?";
                $params[] = $allgs["vegan"];
                $types .= "i"; 
            }

            if (isset($allgs["dairy_free"])) { 
                $base .= "AND rcp_is_dairy_free = ?";
                $params[] = $allgs["dairy_free"];
                $types .= "i"; 
            }

            if (isset($allgs["gluten_free"])) { 
                $base .= "AND rcp_is_gluten_free = ?";
                $params[] = $allgs["gluten_free"]; 
                $types .= "i"; 
            }

            $params [] = $limit;
            $params [] = $offset;
            $types .= "ii";

            $query = "SELECT r.id, r.rcp_title AS name, r.rcp_image AS image, r.rcp_ready_minutes AS ready_in, r.rcp_servings AS servings " . $base . " ORDER BY r.rcp_title ASC LIMIT ? OFFSET ?";

            $res['recipe'] = $this->executeSelectQuery($query, $params, $types);
 
            $query = "SELECT COUNT(*) AS total " . $base;

            $types = substr_replace($types, '', -2);
            array_pop($params);
            array_pop($params);
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
        
            return $res;
        }


    }
?>
