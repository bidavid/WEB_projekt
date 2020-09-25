<?php
session_start();

if(!isset($_SESSION['loggedUsername'])) {
    header("location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <title>Home Page</title>
    <style>
        #table td {
            vertical-align: middle;
        }

        #table th {
            vertical-align: middle;
        }

        .manipulation{
            text-align: center;
        }

        img{
            border-radius: 10%
        }
    </style>
</head>

    <body>

        <div class="jumbotron text-center mb-4 bg-primary">
            <h1 class="text-white">Home Page</h1>
            <p class="text-white">Manipulate with your contacts here:</p>
            <form action = "../scripts/logout.php" method="get">
                <button type="submit" class="btn btn-light btn-sm">
                    Sign out&nbsp;&nbsp;<span class='fa fa-sign-out'>
                </button>
            </form>
        </div>

        <div class="container">
            <!--We want our button aligned with text-->
            <h4 class = "mb-3"> Contacts present in database:</h4>

            <table class="table table-condensed table-hover table-sm" id="table">
                <thead class="thead-dark" id="table-header">
                    <tr>
                        <th>#</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>E-mail</th>
                        <th>Phone-number</th>
                        <th>Image</th>
                        <th class = "manipulation">
                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#customModal" id="btnAddContact">
                                <span class='fa fa-plus-circle'></span>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody id = "table-body">
                </tbody>
            </table>

            <!-- The Modal -->
            <div class="modal fade" id="customModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">Creation dialog</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form id="customForm">
                                <div class="form-row d-flex justify-content-center mb-2">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="mb-0" for="fname">First name: </label>
                                            <input type="text" class="form-control" placeholder="Enter first name" id="fname" name= "fname">
                                            <small class="text-success" id="fnameValidMSG">&#10003; First name valid.</small>
                                            <small class="text-danger" id="fnameInvalidMSG">&#10005; Only a-z/A-Z letters allowed. No whitespace.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="mb-0" for="lname">Last name: </label>
                                            <input type="text" class="form-control" placeholder="Enter last name" id="lname" name="lname">
                                            <small class="text-success" id="lnameValidMSG">&#10003; Last name valid.</small>
                                            <small class="text-danger" id="lnameInvalidMSG">&#10005; Only a-z/A-Z letters allowed. No whitespace.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="mb-0" for="email">E-mail: </label>
                                            <input type="email" class="form-control" placeholder="email@example.com" id="email" name= "email">
                                            <small class="text-success" id="emailValidMSG">&#10003; Email valid.</small>
                                            <small class="text-danger" id="emailInvalidMSG">&#10005; Invalid email pattern.</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="mb-0" for="number">Phone number: </label>
                                            <input type="text" class="form-control" placeholder="Enter phone number" id="phone" name= "phone">
                                            <small class="text-success" id="phoneValidMSG">&#10003; Phone number valid.</small>
                                            <small class="text-danger" id="phoneInvalidMSG">&#10005; Only numbers allowed. No whitespace. Minumum 6 numbers.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="mb-0" for="imageInput">Upload image: </label>
                                            <input class="custom-file" type="file" accept = "image/png, image/jpg, image/jpeg" id="selectImage" name ="imgPath"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <input type="button" class="btn btn-success" id="btnContextual" name ="action" value ="Create">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnClose">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>
    </body>

<!-- jQuery scripts -->
<script>
$(document).ready(function () {
    //Koristit ce se kada korisnik odabere gumb Save nakon edita
    var currentContactID = -1;

    var firstNameValid = false;
    var lastNameValid = false;
    var emailValid = false;
    var phoneValid= false;

    hideAllWarnings();
    fetchContacts();

    $(".modal").on("hidden.bs.modal", function(){
        resetModal();
    });

    $("#btnAddContact").click(function() {
        $("#modalTitle").text("Creation dialog");
        $('#btnContextual').val("Create");
    });

    $(document).on("click", ".edit", function() {
        $("#modalTitle").text("Edit dialog");
        $('#btnContextual').val("Save");
        //Regularni izraz za izvlacenje id-ja kontakta od trenutno kliknutog edit gumba
        secureValidInputs();
        var id = parseInt($(this).attr("id").match(/\d+/));
        console.log(id);
        currentContactID = id;
        fillEditDialog(id);
    });

    $(document).on("click", ".delete", function() {
        //Regularni izraz za izvlacenje id-ja kontakta od trenutno kliknutog edit gumba
        var id = parseInt($(this).attr("id").match(/\d+/));
        console.log(id);
        deleteContact(id);
    });

    $("#btnContextual").on('click', () => {
        //Akcija je uvijek Create ili Save, ovisno o tome radi li se o insert-u ili update-u
        //Ako su inputi u redu
        if(firstNameValid && lastNameValid && emailValid && phoneValid){

            //Dohvati akciju
            var action = $('#btnContextual').val();

            //Dohvati unesene podatke
            var firstName = $("#fname").val();
            var lastName = $("#lname").val();
            var email = $("#email").val();
            var phone = $("#phone").val();

            //Ako se radi o dodavanju novog kontakta
            if(action === "Create"){
                console.log("Dodavanje kontakta");

                //Prvo provjeri je li input type file ispunjen, jer za njega ne postoji boolean metoda validacije
                if ($('#selectImage').get(0).files.length === 0) {
                    //Ako nije odabrana slika
                    alert("No image selected! Cannot continue");
                }else{
                    //Ako je odabrana slika, slijedi dodavanje novog kontakta, a zatim azuriranje tablice u slucaju uspjeha
                    var formData = new FormData();
                    var file = $("#selectImage")[0].files[0];

                    formData.append('action', action);
                    formData.append('imageFile', file);
                    formData.append('fname', firstName);
                    formData.append('lname', lastName);
                    formData.append('email', email);
                    formData.append('phone', phone);

                    $.ajax({
                        url: '../scripts/db_operations/insert_update.php',
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response){
                            $('#customModal').modal("hide");
                            console.log(response);
                            var responseObject = JSON.parse(response);
                            if(responseObject.success){
                                fetchContacts();
                            }else{
                                alert(responseObject.message);
                            }
                        },
                    });
                }

            }else{
                //Ako se radi o editanju, stvori FormData objekt
                var editFormData = new FormData();

                editFormData.append('action', action);
                editFormData.append('fname', firstName);
                editFormData.append('lname', lastName);
                editFormData.append('email', email);
                editFormData.append('phone', phone);
                editFormData.append('IDforEditing', currentContactID);

                 //Ako je input file ispunjen, ubaci datoteku u FormData
                if (!($('#selectImage').get(0).files.length === 0)) {
                    var file = $("#selectImage")[0].files[0];
                    editFormData.append('imageFile', file);

                    $.ajax({
                        url: '../scripts/db_operations/insert_update.php',
                        type: 'post',
                        data: editFormData,
                        contentType: false,
                        processData: false,
                        success: function(response){
                            $('#customModal').modal("hide");
                            console.log(response);

                            var responseObject = JSON.parse(response);
                            if(responseObject.success){
                                fetchContacts();
                            }else{
                                alert(responseObject.message);
                            }
                        },
                    });

                }else{
                    //Ako nije input file nije ispunjen, nastavi bez njega. U bazi ostaje stari path
                    $.ajax({
                        url: '../scripts/db_operations/insert_update.php',
                        type: 'post',
                        data: editFormData,
                        contentType: false,
                        processData: false,
                        success: function(response){
                            $('#customModal').modal("hide");
                            console.log(response);

                            var responseObject = JSON.parse(response);
                            if(responseObject.success){
                                fetchContacts();
                            }else{
                                alert(responseObject.message);
                            }
                        },
                    });

                }
            }

        }else{
            alert("Please fill in all requested fields!")
        }
    });


    $("#fname").on('keyup', ()=>{
        var firstName = $("#fname").val();
        if(firstName.length == 0){
            $("#fnameValidMSG").hide();
            $("#fnameInvalidMSG").hide();
            firstNameValid = false;
        }else{
            validateFirstName(firstName);
        }
    })

    $("#lname").on('keyup', ()=>{
        var lastName = $("#lname").val();
        if(lastName.length == 0){
            $("#lnameValidMSG").hide();
            $("#lnameInvalidMSG").hide();
            lastNameValid = false;
        }else{
            validateLastName(lastName);
        }
    })

    $("#email").on('keyup', ()=>{
        var email = $("#email").val();
        if(email.length == 0 ){
            $("#emailValidMSG").hide();
            $("#emailInvalidMSG").hide();
            emailValid = false;
        }else{
            validateEmail(email);
        }
    })

    $("#phone").on('keyup', ()=>{
        var phoneNumber = $("#phone").val();
        if(phoneNumber.length == 0 ){
            $("#phoneValidMSG").hide();
            $("#phoneInvalidMSG").hide();
            phoneValid = false;
        }else{
            validatePhone(phoneNumber);
        }
    })

    function validateFirstName(firstName){
        firstNameValid = lettersValidation(firstName);
        if(firstNameValid){
            $("#fnameInvalidMSG").hide();
            $("#fnameValidMSG").show();
        }else{
            $("#fnameValidMSG").hide();
            $("#fnameInvalidMSG").show();
        }
    }

    function validateLastName(lastName){
        lastNameValid = lettersValidation(lastName);
        if(lastNameValid){
            $("#lnameInvalidMSG").hide();
            $("#lnameValidMSG").show();
        }else{
            $("#lnameValidMSG").hide();
            $("#lnameInvalidMSG").show();
        }
    }

    function validateEmail(email){
        emailValid = emailPatternValidation(email);
        if(emailValid){
            $("#emailInvalidMSG").hide();
            $("#emailValidMSG").show();
        }else{
            $("#emailValidMSG").hide();
            $("#emailInvalidMSG").show();
        }
    }

    function validatePhone(phoneNumber){
        phoneValid = numbersValidation(phoneNumber) && phoneNumber.length > 5;
        if(phoneValid){
            $("#phoneInvalidMSG").hide();
            $("#phoneValidMSG").show();
        }else{
            $("#phoneValidMSG").hide();
            $("#phoneInvalidMSG").show();
        }
    }

    function lettersValidation(text){
        let specialChars = /[^a-zA-Z]/g;
        if (text.match(specialChars)) {
            return false;
        }else{
            return true;
        }   
    }

    function numbersValidation(text){
        let specialChars = /[^0-9]/g;
        if (text.match(specialChars)) {
            return false;
        }else{
            return true;
        }   
    }

    function emailPatternValidation(text){
        const specialEmailChars = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return specialEmailChars.test(text);
    }


    function resetModal(){
        $('#customForm').trigger("reset");
        currentContactID = -1;

        hideAllWarnings();

        firstNameValid = false;
        lastNameValid = false;
        emailValid = false;
        phoneValid= false;

        console.log("Modal reset!");
    }

    function secureValidInputs(){
        firstNameValid = true;
        lastNameValid = true;
        emailValid = true;
        phoneValid= true;
    }

});


function fetchContacts(){
    $("#table-body").empty();

    $.post(
        "../scripts/db_operations/get_contacts.php",
        {},
        function(response) {
            console.log(response);
            var responseObject = JSON.parse(response);
            console.log(responseObject);

            if(responseObject.success){
            
                var listOfContacts = responseObject.data;

                for(var i = 0; i < listOfContacts.length; i++) {
                    var tableRow = "<tr>";
                    tableRow += "<td>"+listOfContacts[i].id+"</td>";
                    tableRow += "<td>"+listOfContacts[i].fname+"</td>";
                    tableRow += "<td>"+listOfContacts[i].lname+"</td>";
                    tableRow += "<td>"+listOfContacts[i].email+"</td>";
                    tableRow += "<td>"+listOfContacts[i].phone+"</td>";
                    tableRow += "<td><img width='80' height='100' src='" + listOfContacts[i].imgPath + "'></img></td>";
                    tableRow += "<td class = 'manipulation' ><button type ='button' class='btn btn-warning edit' id='edit" + listOfContacts[i].id + "' data-toggle='modal' data-target='#customModal'><span class='fa fa-pencil'></span></button>";
                    tableRow += "&nbsp;&nbsp;";
                    tableRow += "<button type='button' class='btn btn-danger delete' id='delete" + listOfContacts[i].id + "'><span class='fa fa-times'></span></button></td>";
                    tableRow += "</tr>";
                    
                    $("#table-body").append(tableRow);
                }
            }
        }
    );
}


function fillEditDialog(contactID){
    $.post(
        "../scripts/db_operations/get_contact.php",
        {
            "ID": contactID
        },
        function(response) {
            console.log(response);
            var responseObject = JSON.parse(response);

            if(responseObject.success){
                var contact = responseObject.data;

                $("#fname").val(contact.fname);
                $("#lname").val(contact.lname);
                $("#email").val(contact.email);
                $("#phone").val(contact.phone);

            }else{
                $('#customModal').modal("hide");
                alert(responseObject.message)
            }
        }
    )
}

function deleteContact(contactID){
    $.post(
        "../scripts/db_operations/delete_contact.php",
        {
            "ID": contactID
        },
        function(response) {
            console.log(response);
            var responseObject = JSON.parse(response);

            if(responseObject.success){
                fetchContacts();
            }else{
                alert(responseObject.message);
            }
        }
    )
}

function hideAllWarnings(){
            $("#fnameValidMSG").hide();
            $("#fnameInvalidMSG").hide();
            $("#lnameValidMSG").hide();
            $("#lnameInvalidMSG").hide();
            $("#emailValidMSG").hide();
            $("#emailInvalidMSG").hide();
            $("#phoneValidMSG").hide();
            $("#phoneInvalidMSG").hide();
}

</script>
</html>