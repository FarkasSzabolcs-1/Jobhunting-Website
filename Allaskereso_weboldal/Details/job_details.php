<?php
if (empty($_GET['id']) and is_numeric($_GET['id'])) {
    die("Hibás vagy hiányzó azonosító!");
}
else {
$current_page="profile";
session_start();

require_once "../Database\User.php";
require_once "../Database/db_connect.php";
require_once "../Database\Job.php";
require_once "../Database\Application.php";

$categories = [
    'it'                => 'Informatika / IT',
    'finance'           => 'Pénzügy és Könyvelés',
    'management'        => 'Vezetőség / Menedzsment',
    'hr'                => 'Humánerőforrás (HR)',
    'marketing'         => 'Marketing és PR',
    'sales'             => 'Értékesítés és Kereskedelem',
    'admin'             => 'Adminisztráció / Irodai munka',
    'legal'             => 'Jogi terület',
    'logistics'         => 'Logisztika és Szállítás',
    'manufacturing'     => 'Gyártás és Termelés',
    'construction'      => 'Építőipar és Ingatlan',
    'hospitality'       => 'Vendéglátás és Idegenforgalom',
    'customer_service'  => 'Ügyfélszolgálat',
    'skilled_trades'    => 'Szakmunka / Fizikai munka',
    'healthcare'        => 'Egészségügy',
    'education'         => 'Oktatás és Tréning',
    'security'          => 'Biztonságvédelem',
    'agriculture'       => 'Mezőgazdaság',
    'arts'              => 'Művészet és Média',
];
$job=new Job($db);
$sql = "SELECT id,title,company,position,category,city,salary,description,created_by,is_active FROM jobs WHERE id=? LIMIT 1";
$user=new User($db);

$tipusok = "i";
$parameter[] = $_GET['id'];


$job_information = $job->jobInformation($sql, $parameter, $tipusok);
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_job'])) {

        if($job->jobDelete($_GET["id"])){
            header("Location: ../MainPage/profile.php");
            exit;
        }
    }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_confirm'])) {

    $sql="UPDATE jobs SET ";

    $company_name = htmlspecialchars($_SESSION["update"]["company_name"]??"");
    $title = htmlspecialchars($_SESSION["update"]["title"]??"");
    $position = htmlspecialchars($_SESSION["update"]["position"]??"");
    $kategoria = htmlspecialchars(   $_SESSION["update"]["category"]??"");
    $city = htmlspecialchars($_SESSION["update"]["city"]??"");
    $salary = htmlspecialchars($_SESSION["update"]["salary"]??"");
    $description = htmlspecialchars(    $_SESSION["update"]["description"]??"");
    $status =htmlspecialchars(  $_SESSION["update"]["status"]??"");

    $parameter=[];
    $tipusok='';
    if($title){
        $parameter[]=$title;
        $tipusok.="s";
        $sql.="title=?, ";
    }
    if($company_name!=""){
        $parameter[]=$company_name;
        $tipusok.="s";
        $sql.="company=?,  ";
    }

    if($position!=""){
        $parameter[]=$position;
        $tipusok.="s";
        $sql.="position=?, ";
    }
    if($kategoria!=""){
        $parameter[]=$kategoria;
        $tipusok.='s';
        $sql.="category=?, ";
    }
    if($city!=""){
        $parameter[]=$city;
        $tipusok.="s";
        $sql.="city=?,  ";
    }
    if($salary!=""){
        $parameter[]=$salary;
        $tipusok.="d";
        $sql.="salary=?, ";
    }
    if($description!=""){
        $parameter[]=$description;
        $tipusok.="s";
        $sql.="description=?, ";
    }
    if($status!=""){
        $parameter[]=$status;
        $tipusok.="i";
        $sql.="is_active=?,";
    }
    $sql=rtrim($sql,", ");

    $sql.=" WHERE id=? AND created_by=?";
    $parameter[]=(int)$_GET["id"];
    $tipusok.="i";
    $parameter[]=$_SESSION["username"];
    $tipusok.="s";

    $job->jobUpdate($sql,$parameter,$tipusok);

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;

}



    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Álláskereső Portál</title>
    <link rel='stylesheet' href='../styles/styles.css'>
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
    echo "<div class='page'>";


    $company_name = htmlspecialchars($job_information[0][2]);
    $title = htmlspecialchars($job_information[0][1]);
    $position = htmlspecialchars($job_information[0][3]);
    $kategoria = htmlspecialchars($job_information[0][4]);
    $city = htmlspecialchars($job_information[0][5]);
    $salary = htmlspecialchars($job_information[0][6]);
    $description = htmlspecialchars($job_information[0][7]);
    $created_by = htmlspecialchars($job_information[0][8]);
    $status = htmlspecialchars($job_information[0][9]);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_job'])) {

        echo"
<div class='profile_information'>
    <form method='post'>
        <label for='title'>Cím:</label>
        <input type='text' name='title' value='$title' required>

        <label for='company_name'>Cég név:</label>
            <input type='text' name='company_name' value='$company_name' required>

        <label for='position'>Position:</label>
            <input type='text' name='position' value='$position' required>

        <label for='category'>Kategória:</label>
            <select name='category' id='category'  required>
                <option value='$kategoria'></option>";
                    foreach($categories as $id=> $category){
                        echo "<option value='$id'";
                        if($id===$kategoria){
                            echo "selected";}
                            echo ">$category</option>";
                }
            echo "
    </select>
        <label for='city'>Város:</label>
            <input type='text' name='city' value='$city' required>

        <label for='salary'>Bér:</label>
            <input type='number' name='salary' value='$salary' required>Euro

        <label for='description'>Leírás:</label>
            <textarea name='description'  required>$description</textarea><br>
        <label for='active'>
            <input type='radio' id='active' name='status' value='1'";
                if($status==1){
                    echo "checked";
                }
                echo">
            <p class='job_active'>Aktív</p></label>
            <input type='radio' id='inactive' name='status' value='0'";

        if($status==0){
            echo "checked";
        }

                echo">
            <label for='inactive'><p class='job_inactive'>Inaktív</p></label>
        <label for='review''></label>
            <input type='submit' value='Szerkesztés véglegesítése'  name='update_confirm_check'>
    </form>
    </div>";

    }
    elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_confirm_check'])){


        $company_name = htmlspecialchars($_POST["company_name"]??"");
        $title = htmlspecialchars($_POST["title"]??"");
        $position = htmlspecialchars($_POST["position"]??"");
        $kategoria = htmlspecialchars($_POST["category"]??"");
        $city = htmlspecialchars($_POST["city"]??"");
        $salary = htmlspecialchars($_POST["salary"]??"");
        $description = htmlspecialchars($_POST["description"]??"");
        $status =htmlspecialchars($_POST["status"]??"");


        $_SESSION["update"]["company_name"]=$company_name;
        $_SESSION["update"]["title"]=$title;
        $_SESSION["update"]["position"]=$position;
        $_SESSION["update"]["category"]=$kategoria;
        $_SESSION["update"]["city"]=$city;
        $_SESSION["update"]["salary"]=$salary;
        $_SESSION["update"]["description"]=$description;
        $_SESSION["update"]["status"]=$status;


        echo "<div class='profile_information'>
              <h1>Állás információk frissítésének ellenőrzése</h1>
             <legend style='font-size: 30px'>$company_name</legend>";

        $job_1= "
        <p><b>Pozíció: </b> $position</p>
        <div>
        <p style='font-size: 20px'><b>Cég neve:</b> $company_name</p>
        <p><b>Kategória:</b>";

        $job_2= "</p>
        <p><b>Város: </b>$city</p>
        <p><b>Fizetés: </b>$salary euro</p>
        <p><b>Leírás: </b>$description</p>
        <p class='posted_by'><b>Posztolta: {$_SESSION['username']}</b></p>
       </div>
";
        $job_3="</button>
        
<form method='post'>
    
<input type='submit' name='update_confirm' value='Szerkesztés Véglegesítése' >

</form>

</div>
    ";


        echo $job_1;
        if (isset($categories[$kategoria])) {
            echo " ".htmlspecialchars($categories[$kategoria]) . "</p>";
        }
        else{
            echo "Nincs";
        }
        echo $job_2;
        if ($status == 1) {
            echo "<p>Hirdetés Státusza: <a class='job_active'>Aktív</a></p>";
        } else {
            echo "<p>Hirdetés Státusza: <a class='job_inactive'>Inaktív</a></p>";
        }
        echo $job_3;



    }

    else {



        echo "<div class='profile_information'>
                <h1>Hirdetett állás információk</h1>
        <legend style='font-size: 30px'>$company_name</legend>";

        if ($status == 1) {
            echo "<p class='job_active'>Aktív</p>";
        } else {
            echo "<p class='job_inactive'>Inaktív</p>";
        }


        $job_1= "
        
        <p><b>Pozíció: </b> $position</p>
        <div>
        <p style='font-size: 20px'><b>Cég neve:</b> $company_name</p>
        <p><b>Kategória:</b>";

        $job_2= "</p>
        <p><b>Város: </b>$city</p>
        <p><b>Fizetés: </b>$salary euro</p>
        <p><b>Leírás: </b>$description</p>
        <p class='posted_by'><b>Posztolta:</b>$created_by</p>
       </div>
       </button>
        
<form method='post'>
    
    <input type='submit' name='update_job' value='Szerkesztés'>

</form>
<form method='post'>
    
    <input type='submit' id='delete_job' name='delete_job' value='Hirdetés törlése'>

</form>

</div>
    ";
        echo $job_1;
        if (isset($categories[$kategoria])) {
            echo " ".htmlspecialchars($categories[$kategoria]) . "</p>";
        }
        else{
            echo "Nincs";
        }
        echo $job_2;




    $application=new Application($db);
    $application_list=$application->jobApplies($_GET["id"]);

    if(empty($application_list)){
        echo "<h1>Nincsenek jelentkezesek<h1>";
    }
    else{

        echo "
<div class='profile_information'>
<h1>Jelentkezések:</h1>
<table  class=profile_table>
    <tr>
        <th>Név</th>
        <th>Email</th>
        <th>Státusz</th>
        <th>Nagiváció</th>
    </tr>";


        foreach ($application_list as $application){
            $userInfo=$user->userInformation($application[2]);
            $application_status=$application[0];
            echo "<tr><td>".htmlspecialchars($userInfo['username'])."</td>";
            echo "<td>".htmlspecialchars($userInfo['email'])."</td>";
            echo "<td>";
            if($application[0]=="pending"){
                echo "<a id='application_pending'>Függőben lévő</a>";
            }
            if($application[0]=="accepted"){
                echo "<a id='application_accepted'>Elfogadott</>";
            }
            if($application[0]=="rejected"){
                echo "<a id='application_rejected'>Elutasított</>";
            }
            echo "<td><a class='detail_button' href='application_details.php?id=$application[1]'>Részlet</a>";

            echo "</td>
              </tr>";
    }
        echo "</div>";
    }

    }
}

?>