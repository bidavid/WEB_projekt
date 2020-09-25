<?php

session_start();

if(isset($_SESSION['loggedUsername'])) {
    header("location: pages/home_page.php");
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
    <title>Login Form</title>
</head>
<body>

    <div class="jumbotron text-center mb-4 bg-primary">
        <h1 class="text-white">Login Form</h1>
        <p class="text-white">Enter your account login information:</p>
    </div>

    <div class="container">
        <form>
            <div class="form-row d-flex justify-content-center m-3">
                    <div class="form-group col-sm-5">
                        <label class="mb-0" for="uname">Username:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@</div>
                            </div>
                            <input type="text" class="form-control" id="uname" placeholder="ExampleUsername123">
                        </div>
                    </div>
            </div>

            <div class="form-row d-flex justify-content-center m-3">
                    <div class="form-group col-sm-5">
                        <label class="mb-0" for="pwd">Password: </label>
                        <input type="password" class="form-control" placeholder="Choose your password" id="pwd">
                    </div>
            </div>

            <div class="form-row d-flex justify-content-center m-3">
                <input type="button" class="btn btn-primary" id="login" value="Log in" style="display: block;">
            </div>
            <div class="form-row d-flex justify-content-center">
                <p id="loginLink" style="display: block;">Don't have an acccount? Click <a href="pages/register.php">here</a> to register</p>
            </div>
            <div class="form-row d-flex justify-content-center">
                <small class="text-danger" id="inputsInvalidMSG"></small>
            </div>

        </form>
    </div>

</body>

<script>

    $(document).ready( function() {
        hideWarning();
        resetControls();

         $("#login").on('click', () => {
            var username = $("#uname").val();
            var password = $("#pwd").val();
            
            if (username.trim() === "" || password.trim() === "") {
                alert('Please provide all requested data')
                if(username.trim() === ""){
                    $('#uname').val('');
                }
                if(password.trim() === ""){
                    $('#pwd').val('');
                }
            } else {
                $.post( "scripts/action.php",
                    {
                        action: "login",
                        username: username,
                        password: password
                    },
                     function(response){

                        console.log(response);
                        var responseObject = JSON.parse(response);
                        //Ako je zapis u bazu uspjesan, prebaci korisnika na login formu
                        if(responseObject.success){
                            hideWarning();
                            //Prikazi alert s povratnom porukom 
                            alert(responseObject.message);
                            window.location.href = "pages/home_page.php"
                        } else {
                            showWarning(responseObject.message);
                        }
                    }
                );
            }
        })

        $("#pwd").on('keyup', ()=>{
            hideWarning();
        })

        $("#uname").on('keyup', ()=>{
            hideWarning();
        })

        function hideWarning(){
            $("#inputsInvalidMSG").hide();
        }

        function showWarning(text){
            $("#inputsInvalidMSG").show();
            $("#inputsInvalidMSG").html(text);
        }

        function resetControls(){
            $('form :input').val('');
            $("#login").val("Log in");
        }  
    })
</script>
</html>
