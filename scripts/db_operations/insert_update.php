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

    $action = $_POST["action"];
    $firstName=$_POST["fname"];
    $lastName=$_POST["lname"];
    $email=$_POST["email"];
    $phone=$_POST["phone"];

//************************************************************************************************
    //Ako se radi o kreiranju kontakta
    if($action == "Create"){
        //Slika mora biti postavljena kod kreiranja kontakta
        $imageFile=$_FILES["imageFile"];
        //Putanja za pohranu slike unutar projektne mape
        $projectSavingPath = '../../uploads/'.$imageFile['name'];
        //Putanja koju ce index.php koristiti za ucitavanje slika
        $databaseSavingPath = '../uploads/'.$imageFile['name'];


        //Potreban nam je primarni kljuc aktivnog korisnika, da se postavi kao strani kljuc novog kontakta
        $currentUsername = $_SESSION['loggedUsername'];
        $sql = "SELECT id FROM users WHERE uname='{$currentUsername}'";
        $result = $conn->query($sql);

        //Ako uspijes dohvatiti trenutnog korisnika iz baze
        if($result->num_rows === 1){

            $row = $result->fetch_assoc();
            $currentUserID = $row["id"];
            //Ako nema errora i slika nije preko 4mb, pohrani ju u projektnu mapu uploads, insertaj kontakt u bazu te vrati response
            if(( $imageFile['size'] < 4000000) && ($imageFile['error'] === 0)){
    
                move_uploaded_file($imageFile['tmp_name'], $projectSavingPath);
    
                $sql = "INSERT INTO contacts (email, fname, lname, imgPath, phone, userID) VALUES ('$email', '$firstName', '$lastName', '$databaseSavingPath', '$phone', '$currentUserID')";
                
                if( $conn->query($sql)===TRUE){
                    //Contact creation successful
                    $successObject = new responseObject(TRUE, "Contact succesfully created");
                    echo json_encode($successObject);
            
                }else{
                    //Contact creation failed
                    $failureObject = new responseObject(FALSE, "Contact creation failed");
                    echo json_encode($failureObject);
                }
    
            }else{
                $failureObject = new responseObject(FALSE, "Something is wrong with your file");
                echo json_encode($failureObject);
            }

        //Ako ne uspijes dohvatiti trenutnog korisnika iz baze
        }else{
            $failureObject = new responseObject(FALSE, "Failed to fetch current user.");
            echo json_encode($failureObject);
        }
    
        

//***************************************************************************************************** */          
    //Ako se radi o editanju kontakta, dohvati ID kontakta koji editas
    }else{
        $IDforEditing = $_POST["IDforEditing"];

        //Sada $_FILES["imageFile"]; ne mora biti postavljen, zato obavi provjeru;
        if(isset($_FILES["imageFile"])){
            $imageFile=$_FILES["imageFile"];

            $projectSavingPath = '../../uploads/'.$imageFile['name'];

            $databaseSavingPath = '../uploads/'.$imageFile['name'];

            if(( $imageFile['size'] < 4000000) && ($imageFile['error'] === 0)){

                move_uploaded_file($imageFile['tmp_name'], $projectSavingPath);
    
                $sql = "UPDATE contacts SET email='{$email}', fname='{$firstName}', lname='{$lastName}', imgPath='{$databaseSavingPath}', phone ='{$phone}' WHERE id='{$IDforEditing}'";
                
                if( $conn->query($sql)===TRUE){
                    //Update successful
                    $successObject = new responseObject(TRUE, "Contact succesfully updated");
                    echo json_encode($successObject);
            
                }else{
                    //Update failed
                    $failureObject = new responseObject(FALSE, "Contact update failed");
                    echo json_encode($failureObject);
                }
    
            }else{
                $failureObject = new responseObject(FALSE, "Something is wrong with your file");
                echo json_encode($failureObject);
            }
        }else{
            //Ako nije odabrana slika, onda se imgPath nece update-ati
            $sql = "UPDATE contacts SET email='{$email}', fname='{$firstName}', lname='{$lastName}', phone ='{$phone}' WHERE id='{$IDforEditing}'";

            if( $conn->query($sql)===TRUE){
                $successObject = new responseObject(TRUE, "Contact succesfully updated, old picture stayed.");
                echo json_encode($successObject);
        
            }else{
                //Update failed
                $failureObject = new responseObject(FALSE, "Contact update failed");
                echo json_encode($failureObject);
            }
        }
    }

}else{
    $failureObject = new responseObject(FALSE, "Request method is not POST");
    echo json_encode($failureObject);
}


?>