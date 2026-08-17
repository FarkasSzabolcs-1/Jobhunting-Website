<?php
session_start();
require_once "../Database/db_connect.php";
require_once "../Database\User.php";
$message="";
$errors=[];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {


    $username=htmlspecialchars($_POST["username"]??"");
    $email=htmlspecialchars($_POST["email"]??"");
    $password=htmlspecialchars($_POST["password"]??"");
    $password_confirm=htmlspecialchars($_POST["confirm_password"]??"");


    if (empty($username)) {
        $errors["username"] = "A felhasználónév megadása kötelező";
    } elseif (strlen($username) < 4) {
        $errors["username"] = "A név hossza minimum 4 karakter kell legyen.";
    }
    if (empty($password)) {
        $errors["password"] = "Nem lehet üres jelszó.";
    }
    if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
        $errors["email"] = "Hibás email formátum.";
    }
    if (empty($email)) {
        $errors["email"] = "Nem lehet az email üres.";
    }
    if($password!=$password_confirm){
        $errors["confirm_password"]="Nem egyezik meg a ket jelszo.";
    }

    if (empty($errors)) {
        $user=new User($db);
        if($user->userRegister($username,$email,$password)){
            header("Location: login.php");
            exit;
        }
        else{
            $errors["registration_failed"]="Ez a felhasznalonev vagy email mar foglalt.";
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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet"></head>

</head>
<body>
<div id="login" class="login_felulet">
    <h1>Álláskereső Portál</h1>
    <form method="POST" name="login">
        <div class="form_row" >
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" placeholder="Felhasználónév" class="login_input_button">
        </div>
        <div class="form_row">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" class="login_input_button">
        </div>
        <div class="form_row">
            <label for="password">Jelszó:</label>
            <input type="password" name="password" id="password" placeholder="Jelszó" class="login_input_button">
        </div>
        <div class="form_row">
            <label for="password">Jelszó megerősítése:</label>
            <input type="password" id="password" name="confirm_password" placeholder="Jelszó megerősítése" class="login_input_button">
        </div>

        <a href="login.php">Bejelentkezés</a>
        <input type="submit" name="submit"  value="Regisztrálás" id="login_button">

    </form>
</div>
</body>
</html>
