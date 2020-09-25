<?php
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        include "connect.php";
        $email = $_POST["email"];
        $sql = "SELECT * FROM users WHERE email='{$email}'";
        $result = $conn->query($sql);
        echo $result->num_rows;
    }
?>