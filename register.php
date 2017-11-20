<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

#Post data
if (isset($_POST['first_name']) && 
        isset($_POST['last_name']) && 
        isset($_POST['email']) &&
        isset($_POST['password'])
        ) {
    
    $userData = array();
    // receiving the post params
    $userData['firstName'] = $_POST['first_name'];
    $userData['lastName'] = $_POST['last_name'];
    $userData['email'] = $_POST['email'];
    $userData['password'] =  $_POST['password'];
    
    
    // check if user is already existed with the same email
    if ($db->isUserExisted($userData['email'])) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with " . $userData['email'];
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->storeUser($userData);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["user"]["first_name"] = $user["first_name"];
            $response["user"]["last_name"] = $user["last_name"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["created"] = $user["created"];
            $response["user"]["updated"] = $user["updated"];
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email or password) is missing!";
    echo json_encode($response);
}
?>

