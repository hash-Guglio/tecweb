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

        public function getSchemaSelect($searchType, $filterName = "") {

            $filters = [];
            $query = "";

            switch ($searchType) {
                case "ricette":
                    switch ($filterName) {
                        case "dish_type":    
                            $query = "SELECT DISTINCT id, dt_type FROM dish_type;";
                            break;
                        case "allgs":
                            $query = "SELECT id, rst_type, rst_disorder_name FROM restriction;";
                            break;
                        default:
                            $query = "";
                            break;
                    }
                    break;
                case "ingredienti":
                    switch ($filterName) {
                        case "order":
                            return 
                                ["asc" => "più basso", 
                                 "desc" => "più alto"
                                ];
                    }
                    break;

                case "utente":
                    $query = "SELECT SUBSTRING(COLUMN_TYPE, 6, LENGTH(COLUMN_TYPE) - 6) AS genders FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME = 'usr_gender';";
                     break;

                default:
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

        public function signupUser($username, $password) : array {
            $query = "INSERT into user(usr_name, usr_password) VALUES (?, ?)";
            $password = password_hash($password, PASSWORD_DEFAULT);
            $params = [$username, $password];

            return [$this->executeUpdateQuery($query, $params), $this->connection->insert_id];
        }
 
        public function getUsernameByUserId($id): array {
            $query = "SELECT usr_name FROM user  WHERE id = ?";
            $params = [$id];
            $types = "i";

            $result = $this->executeSelectQuery($query, $params, $types);

            return $result;
        }

        public function getUserDataByUserId($id): array {
            $query = "SELECT user.*, restriction.id AS rst_id, restriction.rst_type, restriction.rst_disorder_name FROM user LEFT JOIN user_restriction ON user.id = user_restriction.user LEFT JOIN restriction ON restriction.id = user_restriction.restriction WHERE user.id = ?";
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
            array $usr_restrictions = [],
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
            $userId = $isUpdate ? $id : $this->connection->insert_id;

            $this->updateUserRestrictions($userId, $usr_restrictions);

            return [
                $result,
                $userId
            ];
        }

        private function updateUserRestrictions(int $userId, array $user_restrictions): void {

            $query = "SELECT id, rst_type FROM restriction";
            $restrictionData = $this->executeSelectQuery($query);


            $restrictionMap = [];
            foreach ($restrictionData as $row) {
                $restrictionMap[$row['id']] = $row['id'];
            }

            $activeRestrictionIds = [];
            foreach ($user_restrictions as $restriction) {
                if (isset($restrictionMap[intval($restriction)])) {
                    $activeRestrictionIds[] = $restrictionMap[intval($restriction)];
                }
            }

            $deleteQuery = "DELETE FROM user_restriction WHERE user = ?";
            $this->executeUpdateQuery($deleteQuery, [$userId], "i");

            if (!empty($activeRestrictionIds)) {
                $insertQuery = "INSERT INTO user_restriction (user, restriction) VALUES ";
                $insertParams = [];
                $insertTypes = "";

                foreach ($activeRestrictionIds as $restrictionId) {
                    $insertQuery .= "(?, ?),";
                    $insertParams[] = $userId;
                    $insertParams[] = $restrictionId;
                    $insertTypes .= "ii";
                }

                $insertQuery = rtrim($insertQuery, ",");
                $this->executeUpdateQuery($insertQuery, $insertParams, $insertTypes);
            }

        }

        
        // ==========================
        // Recipe-related Queries
        // ==========================

        public function getRecipeById($id) : array {
            $query = "SELECT * FROM recipe WHERE id = ?";
            $params = [$id];
            $types = "i";

            return $this->executeSelectQuery($query, $params, $types);
        }

        public function getDtByRecipeId($id) : array {
            $query = "SELECT dt.id AS id, dt.dt_type AS name FROM dish_type AS dt JOIN dish_type_recipe AS dtr ON dtr.dish_type = dt.id WHERE dtr.recipe = ?" ;
            $params = [$id];
            $types = "i";

            return $this->executeSelectQuery($query, $params, $types);
        }

        public function getRstByRecipeId($id) : array {
            $query = "SELECT r.rst_type FROM restriction AS r JOIN recipe_restriction AS rr ON r.id = rr.restriction WHERE rr.recipe = ?";
            $params = [$id];
            $types = "i";

            return $this->executeSelectQuery($query, $params, $types);
        
        }

        public function getIngredientsByRecipeId($id) : array {
            $query = "SELECT i.id AS id, i.igr_name AS name, i.igr_image AS image, ri.amount AS amount, i.igr_unit AS unit FROM ingredient i JOIN recipe_ingredient ri ON i.id = ri.ingredient WHERE ri.recipe = ? ORDER BY i.igr_name ASC;"; 

            $params = [$id];
            $types = "i";

            return $this->executeSelectQuery($query, $params, $types);
        }
        
        public function searchRecipe($str, $limit, $offest) : array {
            $base = "FROM recipe WHERE rcp_title LIKE ? ORDER BY rcp_title";

            $res = [];

            $query = "SELECT id, rcp_title AS name, rcp_image AS image, rcp_ready_minutes AS ready_in, rcp_servings AS servings " . $base . " LIMIT ? OFFSET ?";

            $params = ["%". trim($str) . "%", $limit, $offest]; 
            $types = "sii";
            
            $res['result'] = $this->executeSelectQuery($query, $params, $types);

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
            $res['result'] = $this->executeSelectQuery($query, $params, $types);
 
            $query = "SELECT COUNT(*) AS total " . $base;
            $params = ["%". trim($str) . "%", $dish_type];
            $types = "si";
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
        
            return $res;
        }

        public function searchRecipeByAllgs($str, $limit, $offset, $allg) : array {

            $base = "FROM recipe AS r JOIN recipe_restriction AS rr ON r.id = rr.recipe JOIN restriction AS rst ON rst.id = rr.restriction WHERE r.rcp_title LIKE ? AND rr.restriction = ?";

            $params = ["%". trim($str) . "%", $allg];
            $res = [];
            $types = "si";


            $params [] = $limit;
            $params [] = $offset;
            $types .= "ii";

            $query = "SELECT r.id, r.rcp_title AS name, r.rcp_image AS image, r.rcp_ready_minutes AS ready_in, r.rcp_servings AS servings " . $base . " ORDER BY r.rcp_title ASC LIMIT ? OFFSET ?";

            $res['result'] = $this->executeSelectQuery($query, $params, $types);
 
            $query = "SELECT COUNT(*) AS total " . $base;

            $types = substr_replace($types, '', -2);
            array_pop($params);
            array_pop($params);
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
        
            return $res;
        }
        
        // ==========================
        // Ingredient-related Queries
        // ==========================

        public function getNutByIngredientId($id) : array {
            $query = "SELECT n.id AS id, n.ntr_name AS name, inut.amount AS amount, n.ntr_unit AS unit FROM ingredient i JOIN ingredient_nutrient inut ON i.id = inut.ingredient JOIN nutrient n ON inut.nutrient = n.id WHERE i.id = ?";
            $params = [$id];
            $types = "s";

            return $this->executeSelectQuery($query, $params, $types);
        }
        
        public function searchIngredient($str, $limit, $offest) : array {            
            $base = "FROM ingredient JOIN ingredient_category AS ic ON ingredient.id = ic.ingredient JOIN category AS c ON c.id = ic.category WHERE igr_name LIKE ? ORDER BY igr_name";
            $res = [];

            $query = "SELECT ingredient.id, igr_name AS name, igr_image AS image, cat_name AS category " . $base . " LIMIT ? OFFSET ?";

            $params = ["%". trim($str) . "%", $limit, $offest]; 
            $types = "sii";
            
            $res['result'] = $this->executeSelectQuery($query, $params, $types);

            $query = "SELECT COUNT(*) AS total " . $base;
            $params = ["%". trim($str) . "%"];
            $types = "s";
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
            
            return $res;
        }

        public function searchIngredientByNut($str, $limit, $offset, $nutrient, $order) : array {
            $base = "FROM ingredient JOIN ingredient_nutrient ON ingredient.id = ingredient_nutrient.ingredient JOIN nutrient n ON n.id = ingredient_nutrient.nutrient 
                JOIN ingredient_category AS ic ON ingredient.id = ic.ingredient JOIN category AS c ON c.id = ic.category WHERE ingredient.igr_name LIKE ? AND n.id = ? ORDER BY ingredient_nutrient.amount {$order}";
            $res = [];
            
            $query = "SELECT ingredient.id, igr_name AS name, igr_image AS image, cat_name AS category " . $base . " LIMIT ? OFFSET ?";    

            $params = ["%". trim($str) . "%", $nutrient, $limit, $offset];

            $types = "sssi";

            $res['result'] = $this->executeSelectQuery($query, $params, $types);
            
            $query = "SELECT COUNT(*) AS total " . $base;

            $params = ["%". trim($str) . "%", $nutrient];
            $types = "ss";
            $res['count'] = $this->executeSelectQuery($query, $params, $types);
            
            return $res;
        }    
    }
?>
