<?php
session_start();

class responseObject {
    function responseObject ($success, $message) {
        $this->success = $success;
        $this->message = $message;
    }
}

if(!isset($_SESSION['loggedUsername'])) {
    header("location: ../../index.php");

}else if($_SERVER["REQUEST_METHOD"] === "POST") {
    include "../connect.php";

    //Dohvati ID kliknutog kontakta
    $contactID = $_POST['ID'];

    //Sada slijedi dohvacanje kliknutog kontakta iz baze
    $sql = "DELETE FROM contacts WHERE id='{$contactID}'";

    if($conn->query($sql)===TRUE){
        //Delete uspjesan
        $successObject = new responseObject(TRUE, "Successfully deleted");
        echo (json_encode($successObject));

    }else{
        //Ne postoji niti jedan kontakt trenutnog korisnika
        $failureObject = new responseObject(FALSE, "Deletion failed. Please try again.");
        echo json_encode($failureObject);
    }

}else{
    $failureObject = new responseObject(FALSE, "Request method is not POST");
    echo json_encode($failureObject);
}

?>