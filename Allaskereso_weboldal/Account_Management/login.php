<?php
session_start();
require_once "../Database/db_connect.php";
require_once "../Database\User.php";
$message="";
$errors=[];



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $username=htmlspecialchars($_POST["username"]);
    $password=htmlspecialchars($_POST["password"]);

    if (empty($username)) {
        $errors["username"] = "A felhasználónév megadása kötelező";
    } elseif (strlen($username) < 4) {
        $errors["username"] = "A név hossza minimum 4 karakter kell legyen.";
    }
    if (empty($password)) {
        $errors["password"] = "Nem lehet üres jelszó.";
    }
    if (empty($errors)) {
        $user_login=new User($db);
        $user_id=$user_login->userLogin($username,$password);
        if($user_id!=false){
            $userInfo=$user_login->userInformation($user_id);
            $_SESSION["username"]=$username;
            $_SESSION["user_id"]=$user_id;
            header("Location: ../index.php");
            exit;
        }
        else{
            $errors["login_failed"]="Hibas felhasznalonev vagy jelszo.";
            foreach ($errors as $error) {
                $message .= $error . "\n";
            }
            $alert = "<script type='text/javascript'>alert(".json_encode("HIBA!\n".$message).");</script>";
            echo $alert;
        }




    } else {
        foreach ($errors as $error) {
            $message .= $error . "\n";
        }
        $alert = "<script type='text/javascript'>alert(".json_encode("HIBA!\n".$message).");</script>";
        echo $alert;
    }

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Álláskereső Portál</title>
    <link rel="stylesheet" href="/styles/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    </head>


<body>
    <div id="login">
        <h1>Álláskereső Portál</h1>
        <form method="POST" name="login">
            <div class="form_row" >
                <label for="username">Felhasználónév:</label>
                <input type="text" name="username" id="username" class="login_input_button" placeholder="Felhasználónév">
            </div>
            <div class="form_row">
                <label for="password">Jelszó:</label>
                <input type="password" id="password" name="password" class="login_input_button" placeholder="Jelszó">
            </div>

            <a href="register.php">Regisztrálás</a>
            <input type="submit" name="submit"  value="Bejelentkezés" id="login_button">

        </form>
    </div>
</body>
</html>