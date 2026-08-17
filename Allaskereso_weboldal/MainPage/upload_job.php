<?php
session_start();
require_once "../Database/db_connect.php";
require_once "../Database\User.php";
require_once "../Database\Job.php";

$current_page="job_upload";



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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['review'])) {

    $title=htmlspecialchars($_POST["title"]??"");
    $company_name=htmlspecialchars($_POST["company_name"]??"");
    $position=htmlspecialchars($_POST["position"]??"");
    $category=htmlspecialchars($_POST["category"]??"");
    $city=htmlspecialchars($_POST["city"]??"");
    $salary=htmlspecialchars($_POST["salary"]??"");
    $description=htmlspecialchars($_POST["description"]??"");

    $_SESSION["job_upload"]["title"]=$title;
    $_SESSION["job_upload"]["company_name"]=$company_name;
    $_SESSION["job_upload"]["position"]=$position;
    $_SESSION["job_upload"]["category"]=$category;
    $_SESSION["job_upload"]["city"]=$city;
    $_SESSION["job_upload"]["salary"]=$salary;
    $_SESSION["job_upload"]["description"]=$description;

    foreach ($categories as $id=>$category){
        if($id===$_SESSION["job_upload"]["category"]){
            $kategoria=$category;
            break;
        }
    }

    $verify_form="
<div class='profile_information'>
        <h1 style='font-size: 30px'>$company_name</h1>
        <h2>$title</h2>
        <p><b>Position:</b> $position</p>
        <fieldset>
            <legend>Részletek</legend>
            <p><b>Kategoria:</b> $kategoria</p>
            <p><b>Város: </b>$city</p>
            <p><b>Fizetes: </b>$salary euro</p>
            <p><b>Leiras:</b>$description</p>

        </fieldset>
            <form method='post'>
                <input type='submit' name='upload_confirm' value='Véglegesítés'>
            </form>
</div>
    ";

}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_confirm'])) {
    $title=$_SESSION["job_upload"]["title"];
    $company_name=$_SESSION["job_upload"]["company_name"];
    $position=$_SESSION["job_upload"]["position"];
    $category=$_SESSION["job_upload"]["category"];
    $city=$_SESSION["job_upload"]["city"];
    $salary=$_SESSION["job_upload"]["salary"];
    $description=$_SESSION["job_upload"]["description"];
    $created_by=$_SESSION["username"];
    $salary_float=(float)$salary;

    if($job->jobUpload($title,$company_name,$position,$category,$city,$salary_float,$description,$created_by)){
        echo "<h4 class='apply_successful'>Sikeres Állásfeltöltés!</h4>";
    }

    unset($_SESSION['job_upload']);
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
</header>";
?>
<div class="page">
    <div class='profile_information'>
    <form method="post">
        <label for="title">Cím:</label>
        <input type="text" id="title" name="title" value='<?=$_SESSION['job_upload']['title']??""?>' required>

        <label for="company_name">Cég név:</label>
            <input type="text" id="company_name" name="company_name" value='<?=$_SESSION['job_upload']['company_name']??""?>' required>

        <label for="position">Position:</label>
            <input type="text" id="position" name="position" value='<?=$_SESSION['job_upload']['position']??""?>' required>

        <label for="category">Kategória:</label>
            <select name="category" id="category"  required>
                <option value='<?=$_SESSION['job_upload']['category']??""?>'></option>
                <?php
                    foreach($categories as $id=> $category){
                        echo "<option value='$id'";
                        if(isset($_SESSION["job_upload"]["category"]) && $_SESSION["job_upload"]["category"]===$id){
                            echo "selected";}
                            echo ">$category</option>";
                }
                ?>
            </select>

        <label for="city">Város:</label>
            <input type="text" id="city" name="city" value='<?=$_SESSION['job_upload']['city']??""?>' required>

        <label for="salary">Bér:</label>
            <input type="number" id="salary" name="salary" value='<?=$_SESSION['job_upload']['salary']??""?>' required>Euro

        <label for="description">Leírás:</label>
            <textarea name="description" id="description"  required><?=$_SESSION['job_upload']['description']??""?></textarea>
        <label for="review"></label>
        <input type="submit" value="Előnézet"  name="review">
    </form>
    </div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['review'])) {
    echo $verify_form;

}

?>

</div>

</body>
</html>