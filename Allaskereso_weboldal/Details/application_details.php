<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Hibás vagy hiányzó azonosító!");
}
else{
    session_start();
    require_once "../Database/db_connect.php";
    require_once "../Database/Application.php";
    require_once "../Database/Job.php";
    require_once "../Database/User.php";

    $current_page="profile";

    $user=new User($db);
    $application=new Application($db);

    $application_info=$application->applicationInfo($_GET['id']);
    $job=new Job($db);

    $job_info_params[]=$application_info[0][3];

    $job_creator=$job->jobInformation("SELECT id,created_by FROM jobs WHERE id=?",$job_info_params,"i");
    $user_info=$user->userInformation($application_info[0][2]);

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

        if($application->applicationDelete($_GET["id"])){
            header("Location: ../MainPage/profile.php");
            exit;
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_status_update'])) {
        $status_update=trim($_POST["apply_status"]?? '');
        if($status_update!==""){
            $application->applicationStatusUpdate($status_update,$_GET["id"]);
            $location="application_details.php?id=".$_GET["id"];
            header("Location: $location");
            exit;
        }
    }



    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Álláskereső Portál</title>
    <link rel='stylesheet' href='/styles/styles.css'>
    <link href='https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap' rel='stylesheet'>
</head>
<body>";
    require_once("../Account_Management/header_logged_in.php");

    echo "
<header>
    <h1>Álláskereső Portál</h1>
    <div id='logged_in'>
    <a href='../index.php'";
    echo ($current_page == 'index') ?  "class='active'" : '';
    echo ">Állások</a>
<a href='../MainPage/profile.php'";
    echo ($current_page == 'profile') ? "class='active'" : '';
    echo ">Profil</a>
        <a href='../MainPage/upload_job.php'";
    echo ($current_page == 'job_upload') ? "class='active'" : '';

    echo ">Feltöltés</a>
<span>Üdvözöllek ";

    echo htmlspecialchars($_SESSION["username"]);
    echo "
</span>
<form method='POST' name='logout' >
    <input type='submit' name='logout' value='Log out'>
</form>
</div>
</header>";

echo "
<div class='page'>
    <div class='profile_information'>
       <h1>Jelentkezési Információk</h1>
       <h3>Felhasználónév: {$user_info['username']}</h3>
       <h3>E-Mail: {$user_info['email']}</h3>
       <h3>Önéletrajz: ";

if(empty($user_info['cv'])){
    echo "Nincs önéletrajz feltöltve.</h3>";
}
else{
    echo "<a href='../{$user_info["cv"]}' download='{$user_info["username"]}_cv'>Önéletrajz letöltése</a></h3>";
}
    echo "<h3>Jelentkezés Státusza: ";
    if($application_info[0][1]=="pending"){
            echo "<a id='application_pending'>Függőben lévő</a>";

        }
    else if($application_info[0][1]=="accepted"){
            echo " <a id='application_accepted'>Elfogadva</a>";

        }
        else if($application_info[0][1]=="rejected"){
            echo "<a id='application_rejected'>Elutasítva</a>";

        }
    echo "</h3>";

    echo "<fieldset>
    <legend>Jelentkezés kezelőfelület";
    if($job_creator[0][1]===$_SESSION["username"]){
        echo "(Admin felület)</legend>";

        echo "
                    <form method='post'>
                        <label><input type='radio' id='pending}' name='apply_status' value='pending'>Függőben</label>
    
                        <label><input type='radio' id='rejected]}' name='apply_status' value='rejected'>Elutasítva</label>
    
                        <label><input type='radio' id='pending' name='apply_status' value='accepted'>Elfogadva</label>
                        <input type='submit' name='application_status_update'>
                    </form>
                    
                    <form method='post'>
                         <input type='submit' name='delete' value='Jelentkezés visszavonása'>
                    </form>
                    
                   </fieldset>";

    }
    else{
        echo "(Felhasználó felület)</legend>";


            if($application_info[0][1]!="pending"){
                echo "<h4>Csak PENDING státuszu jelentkezéseket lehet visszavonni</h4>";
        }else{
                echo "
                <form method='post'>
                    <input type='submit' name='delete' value='Jelentkezés visszavonása'>
                </form>";
            }
    }
    echo "</div></div>";
}