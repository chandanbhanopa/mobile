<?php

/**
 * 
 * Database function classes
 */

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($data = array()) {
       
        $hash = $this->hashSSHA($data['password']);
        $password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $stmt = $this->conn->prepare("INSERT INTO users(first_name, last_name, email, password, salt, created) VALUES(?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $data['firstName'], $data['lastName'], $data['email'], $password, $salt);
        $result = $stmt->execute();
        $stmt->close();
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            
            $stmt->close();
            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['password'];
            
            $hash = $this->checkhashSSHA($salt, $password);
           
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email = "") {
        
        $stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");
        if($stmt) {
            $stmt->bind_param("s", $email);

                $stmt->execute();

                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // user existed 
                    $stmt->close();
                    return true;
                } else {
                    // user not existed
                    $stmt->close();
                    return false;
                }
        } else {
            $error = $this->conn->errno . ' ' . $this->conn->error;
            echo $error; // 1054 Unknown column 'foo' in 'field list'
        }
        
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

}

?>
