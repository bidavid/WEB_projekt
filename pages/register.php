<?php

session_start();

if(isset($_SESSION['loggedUsername'])) {
    header("location: home_page.php");
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
    <title>Registration Form</title>
</head>
<body>

    <div class="jumbotron text-center mb-4 bg-primary">
        <h1 class="text-white">Registration Form</h1>
        <p class="text-white">Enter account creation information:</p>
    </div>

    <div class="container">
        <form>
            <div class="form-row">
                <div class="col-sm-6">
                    <h4>Personal info:</h4>
                    <div class="form-group">
                        <label class="mb-0" for="fname">First name: </label>
                        <input type="text" class="form-control" placeholder="Enter your first name" id="fname">
                        <small class="text-success" id="fnameValidMSG">&#10003; First name valid.</small>
                        <small class="text-danger" id="fnameInvalidMSG">&#10005; Only a-z/A-Z letters allowed. No whitespace.</small>
                    </div>
                    <div class="form-group">
                        <label class="mb-0" for="lname">Last name: </label>
                        <input type="text" class="form-control" placeholder="Enter your last name" id="lname">
                        <small class="text-success" id="lnameValidMSG">&#10003; Last name valid.</small>
                        <small class="text-danger" id="lnameInvalidMSG">&#10005; Only a-z/A-Z letters allowed. No whitespace.</small>
                    </div>
                    <div class="form-group">
                        <label class="mb-0" for="email">E-mail: </label>
                        <input type="email" class="form-control" placeholder="email@example.com" id="email">
                        <small class="text-success" id="emailValidMSG">&#10003; Email valid.</small>
                        <small class="text-danger" id="emailInvalidMSG">&#10005; Email invalid or account with this email already exists.</small>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>     
                </div>

                 <div class="col-sm-6">
                    <h4>Account info:</h4>
                    <div class="form-group">
                        <label class="mb-0" for="uname">Username:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@</div>
                            </div>
                            <input type="text" class="form-control" id="uname" placeholder="ExampleUsername123">
                        </div>
                        <small class="text-success" id="usernameValidMSG">&#10003; Username valid.</small>
                        <small class="text-danger" id="usernameInvalidMSG">&#10005; Username not valid/available. Use minimum 5 letters/digits.</small>
                    </div>
                    <div class="form-group">
                        <label class="mb-0" for="pwd">Password: </label>
                        <input type="password" class="form-control" placeholder="Choose your password" id="pwd">
                        <small class="text-success" id="passwordValidMSG">&#10003; Password valid.</small>
                        <small class="text-danger" id="passwordInvalidMSG">&#10005; Use only letters/digits. Password length should be between 8 and 25 characters long inclusive.</small>
                    </div>
                    <div class="form-group">
                        <label class="mb-0" for="cfpwd">Confirm password: </label>
                        <input type="password" class="form-control" placeholder="Confirm chosen password" id="cfpwd">
                        <small class="text-success" id="passwordConfirmationValidMSG">&#10003; Passwords match.</small>
                        <small class="text-danger" id="passwordConfirmationInvalidMSG">&#10005; Passwords don't match.</small>
                        <small id="emailHelp" class="form-text text-muted">We'll never ask you for your password. Make sure to remember that.</small>
                    </div> 
                </div>
            </div>
            <div class="form-row d-flex justify-content-center m-3">
                <input type="button" class="btn btn-primary" id="register" value="Register" style="display: block;">
            </div>
            <div class="form-row d-flex justify-content-center">
                <p id="loginLink" style="display: block;">Already have an acccount? Log in <a href="../index.php">here</a></p>
            </div>
        </form>
    </div>

</body>

<script>
    $(document).ready( function() {

        hideAllWarnings();
        resetControls();

        var firstNameValid = false;
        var lastNameValid = false;
        var emailValid = false;
        var usernameValid= false;
        var passwordValid = false;
        var passwordConfirmationValid = false;

        $("#register").on('click', () => {  

            console.log(firstNameValid && lastNameValid && emailValid && usernameValid && passwordValid && passwordConfirmationValid)

            if (firstNameValid && lastNameValid && emailValid && usernameValid && passwordValid && passwordConfirmationValid) {
                $.post( "../scripts/action.php",
                    {
                        action: "registration",
                        firstName: $("#fname").val(),
                        lastName: $("#lname").val(),
                        email:  $("#email").val(),
                        username: $("#uname").val(),
                        password: $("#pwd").val()

                    },
                     function(response){
                        console.log(response);

                        var responseObject = JSON.parse(response);
                        //Prikazi alert s povratnom porukom 
                        alert(responseObject.message);
                        //Ako je zapis u bazu uspjesan, prebaci korisnika na login formu
                        if(responseObject.success){
                        window.location.href = "../index.php"
                        }       
                    }
                );
            }
            else{
                alert('Please provide appropriate information before proceeding');
            }
        })

        $("#fname").on('keyup', ()=>{
            console.log(firstNameValid)
            var firstName = $("#fname").val();
            if(firstName.length == 0){
                $("#fnameValidMSG").hide();
                $("#fnameInvalidMSG").hide();
                firstNameValid = false;
            }else{
                validateFirstName(firstName);
                console.log(firstNameValid)
            }
        })

        $("#lname").on('keyup', ()=>{
            console.log(lastNameValid)
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
            console.log(emailValid)
            var email = $("#email").val();
            if(email.length == 0 ){
                $("#emailValidMSG").hide();
                $("#emailInvalidMSG").hide();
                emailValid = false;
            }else{
                //Prvo obavimo validaciju da postavimo emailValid, da korisnik ne moze
                //unijeti < ili >
                validateEmail(email);
                //Ako je unos u redu sto se tice znakova, provjeri postoji li takav email u bazi
                //Ako postoji, emailValid vrati na false, te ispisi error poruku
                if(emailValid){
                    $.post(
                       "../scripts/check_email.php",
                       {
                           "email": email
                       },
                       function(response) {
                           if(response != 0) {
                                $("#emailValidMSG").hide();
                                $("#emailInvalidMSG").show();
                                emailValid = false; 
                           }
                       }
                   );
                }
            }
        })

        $("#uname").on('keyup', ()=>{
            console.log(usernameValid)
            var username = $("#uname").val();
            if(username.length == 0){
                $("#usernameValidMSG").hide();
                $("#usernameInvalidMSG").hide();
                usernameValid = false;
            }else{
                validateUsername(username);

                if(usernameValid){
                    $.post(
                       "../scripts/check_username.php",
                       {
                           "uname": username
                       },
                       function(response) {
                           if(response != 0) {
                                $("#usernameValidMSG").hide();
                                $("#usernameInvalidMSG").show();
                                usernameValid = false; 
                           }
                       }
                   );
                }
            }
        })

        $("#pwd").on('keyup', ()=>{
            console.log(passwordValid)
            var password = $("#pwd").val();
            validatePasswordConfirmation($("#cfpwd").val(),password)
            if(password.length == 0){
                $("#passwordValidMSG").hide();
                $("#passwordInvalidMSG").hide();
                passwordValid = false;
            }else{
                validatePassword(password);
            }
        })

        $("#cfpwd").on('keyup', ()=>{
            console.log(passwordConfirmationValid)
            var passwordConfirmation = $("#cfpwd").val();
            if(passwordConfirmation.length == 0){
                $("#passwordConfirmationValidMSG").hide();
                $("#passwordConfirmationInvalidMSG").hide();
                passwordConfirmationValid = false;
            }else{
                validatePasswordConfirmation(passwordConfirmation, $("#pwd").val());
                console.log(passwordConfirmationValid)
            }
        })

        function hideAllWarnings(){
            $("#fnameValidMSG").hide();
            $("#fnameInvalidMSG").hide();
            $("#lnameValidMSG").hide();
            $("#lnameInvalidMSG").hide();
            $("#emailValidMSG").hide();
            $("#emailInvalidMSG").hide();
            $("#usernameValidMSG").hide();
            $("#usernameInvalidMSG").hide();
            $("#passwordValidMSG").hide();
            $("#passwordInvalidMSG").hide();
            $("#passwordConfirmationValidMSG").hide();
            $("#passwordConfirmationInvalidMSG").hide();
        }

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

        function validateUsername(username){
            usernameValid = lettersAndDigitsValidation(username) && username.length > 4;
            if(usernameValid){
                $("#usernameInvalidMSG").hide();
                $("#usernameValidMSG").show();
            }else{
                $("#usernameValidMSG").hide();
                $("#usernameInvalidMSG").show();
            }
        }

        function validatePassword(password){
            passwordValid = (lettersAndDigitsValidation(password) && password.length > 7 && password.length < 26);
            if(passwordValid){
                $("#passwordInvalidMSG").hide();
                $("#passwordValidMSG").show();
            }else{
                $("#passwordValidMSG").hide();
                $("#passwordInvalidMSG").show();
            }
        }

        function validatePasswordConfirmation(passwordConfirmation, password){
            passwordConfirmationValid = (passwordConfirmation === password);
            if(passwordConfirmationValid){
                $("#passwordConfirmationInvalidMSG").hide();
                $("#passwordConfirmationValidMSG").show();
            }else{
                $("#passwordConfirmationValidMSG").hide();
                $("#passwordConfirmationInvalidMSG").show();
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

        function emailPatternValidation(text){
            const specialEmailChars = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return specialEmailChars.test(text);
        }

        function lettersAndDigitsValidation(text){
            let specialChars = /[^a-zA-Z0-9]/g;
            if (text.match(specialChars)) {
                return false;
            }else{
                return true;
            }   
        }     

        function resetControls(){
            $('form :input').val('');
            $("#register").val("Register");
        }     
    })
</script>
</html>