<?php
    require_once("ini.php");

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    class Database {
        private $connection;
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
    }

?>
