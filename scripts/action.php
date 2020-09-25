<?php
class responseObject {
    function responseObject ($success, $message) {
        $this->success = $success;
        $this->message = $message;
    }
}

if($_SERVER["REQUEST_METHOD"] === "POST") {
    include "connect.php";

    $action = $_POST["action"];
    $username=$_POST["username"];
    $passwordmd5=md5($_POST["password"]);

    if($action == "login"){
        //Attempting to identify user
        $sql = "SELECT * FROM users WHERE uname='{$username}' AND password='{$passwordmd5}'";
        $result = $conn->query($sql);
    
        if($result->num_rows > 0){
            //Login successful
            $successObject = new responseObject(TRUE, "Login successful!");
            session_start();
            $_SESSION['loggedUsername'] = $username;
            echo json_encode($successObject);
    
        }else{
            //Login failed
            $failureObject = new responseObject(FALSE, "It looks like you don't have an account. Please try again or go to registration page.");
            echo json_encode($failureObject);
        }
        
    }else{
        //Starting registration
        $firstName=$_POST["firstName"];
        $lastName=$_POST["lastName"];
        $email=$_POST["email"];
        
        $sql = "INSERT INTO users (email, uname, fname, lname, password) VALUES ('$email', '$username', '$firstName', '$lastName', '$passwordmd5')";
        
        if( $conn->query($sql)===TRUE){
            //Registration successful
            $successObject = new responseObject(TRUE, "Registration successful! Please log in to continue.");
            echo json_encode($successObject);
    
        }else{
            //Registration failed
            $failureObject = new responseObject(FALSE, "We are sorry {$firstName}, but something went wrong. Please try again.");
            echo json_encode($failureObject);
        }

    }
}else{
    $failureObject = new responseObject(FALSE, "We are sorry, but wrong HTTP method was used.");
    echo json_encode($failureObject);
}
?>
