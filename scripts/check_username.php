<?php
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        include "connect.php";
        $username = $_POST["uname"];
        $sql = "SELECT * FROM users WHERE uname='{$username}'";
        $result = $conn->query($sql);
        echo $result->num_rows;
    }
?>