<?php
session_start();
require_once "../Database\User.php";
require_once "../Database/db_connect.php";
require_once "../Database\Job.php";
$user=new User($db);
$job=new Job($db);
$current_page="profile";

$userinformation=$user->userInformation($_SESSION["user_id"]);

$uploaded_jobs=$user->userCreatedJobs($_SESSION["username"]);

$username=htmlspecialchars($userinformation['username']??"");
$email=htmlspecialchars($userinformation["email"]??"");
$id="id";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_cv'])) {
    $target_dir="../users_cv/";
    $file=$_FILES["cv_upload"];

    $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

    $allowed_files=['pdf','doc','docx'];

    if(in_array($ext,$allowed_files)&& $file["size"]<=10*1024*1024){
        if($file["error"]===0){
            $uj_nev="cv_".$_SESSION["username"].".".$ext;
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            move_uploaded_file($file["tmp_name"],$target_dir.$uj_nev);


            if($user->cvUpdate($_SESSION["user_id"],$target_dir.$uj_nev)) {
                echo "sikeres feltöltés!";
                header("Location: profile.php");
                exit;
            }
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Álláskereső Portál</title>
    <link rel="stylesheet" href="/styles/styles.css">
    <link href='https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap' rel='stylesheet'>

</head>
<body>
<?php require_once("../Account_Management/header_logged_in.php");
echo "
<header>
    <h1>Álláskereső Portál</h1>
    <div id='logged_in'>
    <a href='../index.php'";
echo ($current_page == 'index') ?  "class='active'" : '';
echo ">Állások</a>
<a href='profile.php'";
echo ($current_page == 'profile') ? "class='active'" : '';
echo ">Profil</a>
        <a href='upload_job.php'";
echo ($current_page == 'job_upload') ? "class='active'" : '';

echo ">Feltöltés</a>
<span>Üdvözöllek ";

echo htmlspecialchars($_SESSION["username"]);
echo "</span>
<form method='POST' name='logout' >
    <input type='submit' name='logout' value='Log out'>
</form>
</div>
</header>"; ?>
<div class="page">
    <div class="profile_information">
    <h1>Felhasználói adatok</h1>
    <table class='profile_table'>
        <tr id="table_row">
            <td>Felhasználónév:</td>
            <td><?php echo $username?></td>
        </tr>
        <tr id="table_row ">
            <td>Email:</td>
            <td><?php echo $email?></td>
        </tr>
        <tr id="table_row">
            <td>Önéletrajz:</td>
            <td>
        <?php
        if(empty($userinformation["cv"])){
            echo "Nincs feltöltve";
        }
        else{
            echo "<a href='../{$userinformation['cv']}' download='{$userinformation["username"]}_cv' id='cv_download_button'>Önéletrajz letöltése</a></h3>";

        }
        ?>
            </td>
        </tr>
        </table>
    </div>
    <div class="profile_information">
        <h1>CV feltöltés</h1>
        <form method='POST' class="cv_form" enctype="multipart/form-data">
        <div>
            <input type='file' name='cv_upload' accept=".pdf,.doc,.docx" required>
            <input type='submit' name='submit_cv' value='Feltöltés' >
        </div>
        </form>
    </div>
    <div>
    <div class="profile_information">
        <h1>Jelentkezések</h1>
<?php
$applied_jobs=$user->userApplies($userinformation["id"]);





if(empty($applied_jobs)){
    echo "<h4>(Nincsenek állásra jelentkezések)</h4>";
}else {

    echo "<table class='profile_table'>
            <tr>
                <th>Cím</th>
                <th>Hírdető Cég</th>
                <th>Státusz</th>
                <th>Navigáció</th>
            </tr>";
    echo "<br>";
    foreach ($applied_jobs as $application) {
        $parameter = [];
        $sql = "SELECT id,title,company,position,category,city,salary,description,created_by FROM jobs WHERE id=? LIMIT 1";

        $tipusok = "i";
        $parameter[] = $application[1];


        $job_information = $job->jobInformation($sql, $parameter, $tipusok);


        $job_title = htmlspecialchars($job_information[0][1]);
        $job_company=htmlspecialchars($job_information[0][2]);
        $status=$application[3];

        echo "
                <tr>
                <td>$job_title</td>
                <td>$job_company</td>
                <td>";

        if($status=="pending"){
            echo "<a id='application_pending'>Függőben lévő</a>";
        }
        if($status=="accepted"){
            echo " <a id='application_accepted'>Elfogadott</a>";
        }
        if($status=="rejected"){
            echo " <a id='application_rejected'>Elutasított</a>";
        }

        echo"</td>
                <td><a class='detail_button' href='../Details/application_details.php?id=$application[0]'>Részlet</a></td>
            </tr>
";

    }
}
echo "</table>";
?>

    </div>

    </div>
    <div class="profile_information">
    <div>
        <h1>Állás hirdetések</h1>
        <?php
        if(empty($uploaded_jobs)){
            echo "<h4>(Nincsenek feltöltött állások)</h4>";
        }
        else{
            echo "<table class='profile_table'>
            <tr>
                <th>Cím</th>
                <th>Pozíció</th>
                <th>Státusz</th>
                <th>Navigáció</th>
            </tr>";
            foreach($uploaded_jobs as $job){
                $title=htmlspecialchars($job[1]);
                $position=htmlspecialchars($job[3]);
                $status=htmlspecialchars($job[9]);
                if($status==1){
                    $status="active";
                }
                else{
                    $status="inactive";
                }
                $job_id=htmlspecialchars($job[0]);
                echo "
                <tr>
                <td>$title</td>
                <td>$position</td>
                <td>";

                if($status=="active"){
                    echo "<a class='job_active'>Aktív</a>";
                }
                else{
                    echo "<a class='job_inactive'>Inaktív</a>";
                }



                echo"</td>
                <td><a class='detail_button' href='../Details/job_details.php?id=$job_id'>Részlet</a></td>           
            </tr>
        
";

            }
            echo '</table>';
        }
        ?>

        </div>

    </div>
</div>

</body>
</html>

