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

    //Dohvati ID kliknutog kontakta
    $contactID = $_POST['ID'];

    //Sada slijedi dohvacanje kliknutog kontakta iz baze
    $sql = "SELECT * FROM contacts WHERE id='{$contactID}'";
    $result = $conn->query($sql);

    if($result->num_rows === 1){
        //Ocitavanje uspjesno
        $clickedContact = $result->fetch_assoc();

        $successObject = new responseObject(TRUE, $clickedContact);
        echo (json_encode($successObject));

    }else{
        //Ne postoji niti jedan kontakt trenutnog korisnika
        $failureObject = new responseObject(FALSE, "Looks like there are no contacts available for current user.");
        echo json_encode($failureObject);
    }

}else{
    $failureObject = new responseObject(FALSE, "Request method is not POST");
    echo json_encode($failureObject);
}

?>