<?php
/**
 * Class for database connection and will return connection object
 */
class DB_Connect {
    private $conn;

    // Connecting to database
    public function connect() {
        require_once 'include/Config.php';
        
        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        } else {
            //die("connected");
        }
        // return database handler
        return $this->conn;
    }
}

?>
