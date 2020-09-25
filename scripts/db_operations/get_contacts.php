<?php
session_start();

class responseObject {
    function responseObject ($success, $data) {
        $this->success = $success;
        $this->data = $data;
    }
}

if(!isset($_SESSION['loggedUsername'])) {
    header("location: ../../index.php");

}else if($_SERVER["REQUEST_METHOD"] === "POST") {
    include "../connect.php";
    //Dohvati userov ID zato jer je on strani kljuc svih njegovih kontakata

    $currentUsername = $_SESSION['loggedUsername'];
    $sql = "SELECT id FROM users WHERE uname='{$currentUsername}'";
    $result = $conn->query($sql);

    //Ako uspijes dohvatiti trenutnog korisnika iz baze
    if($result->num_rows === 1){

        $row = $result->fetch_assoc();
        $currentUserID = $row["id"];

        //Sada slijedi dohvacanje svih userovih kontakata
        $sql = "SELECT * FROM contacts WHERE userID='{$currentUserID}'";
        $result = $conn->query($sql);
    
        if($result->num_rows > 0){
            //Ocitavanje uspjesno
            while($row = $result->fetch_assoc()) {
                $listOfContacts[] = $row;
            }
            $successObject = new responseObject(TRUE, $listOfContacts);
            echo (json_encode($successObject));
    
        }else{
            //Ne postoji niti jedan kontakt trenutnog korisnika
            $failureObject = new responseObject(FALSE, "Looks like there are no contacts available for current user.");
            echo json_encode($failureObject);
        }

    
        //Ako ne uspijes dohvatiti trenutnog korisnika iz baze
    }else{
        $failureObject = new responseObject(FALSE, "Failed to fetch current user.");
        echo json_encode($failureObject);
    }


}else{
    $failureObject = new responseObject(FALSE, "Request method is not POST");
    echo json_encode($failureObject);
}

?>