<?php
session_start();
require_once "Database\db_connect.php";
require_once "Database\User.php";
require_once "Database\Job.php";
require_once 'Database\Application.php';


$jobs=new Job($db);
$current_page="index";

$max_salary = 0;
$positions = [];
$categories = [];
$cities = [];



$sql_lekerdezes = "SELECT id,title, company, position,category,city,salary,description,created_by FROM jobs WHERE is_active=1";
$parameterek = [];
$tipusok = [];
$joblist = $jobs->jobInformation($sql_lekerdezes, $parameterek, $tipusok);


foreach ($joblist as $job) {
    $title = $job[1];
    $company_name = $job[2];
    $position = $job[3];
    $category = $job[4];
    $city = $job[5];
    $salary = $job[6];
    $description = $job[7];
    $created_by = $job[8];
    if ($job[6] > $max_salary) {
        $max_salary = $job[6];
    }
    if (!in_array($job[3], $positions)) {
        $positions[] = $job[3];
    }
    if (!in_array($job[5], $cities)) {
        $cities[] = $job[5];
    }
    if (!in_array($job[4], $categories)) {
        $categories[] = $job[4];
    }



}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_filter'])) {
    unset($_SESSION["filter"]);

}


$categories_full = [
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
<?php
if(!isset($_SESSION["username"])){
    header("Location: Account_Management/login.php");
    exit;
}
else{


    require_once("Account_Management/header_logged_in.php");
    echo "<header>
    <h1>Álláskereső Portál</h1>
    <div id='logged_in'>
        <a href='index.php'";
     echo ($current_page == 'index') ?  "class='active'" : '';
    echo ">Állások</a>
<a href='MainPage/profile.php'";
    echo ($current_page == 'profile') ? "class='active'" : '';
    echo ">Profil</a>
        <a href='MainPage/upload_job.php'";
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


}
?>
<div class="page_listing">

    <div class="jobs">
       <?php

       if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filter_submit'])) {
           $filter_sql="SELECT id,title, company, position,category,city,salary,description,created_by FROM jobs WHERE is_active=1";
           $parameterek=[];
           $sql_where=[];
           $tipusok="";
           $filter_category=htmlspecialchars($_POST["filter_category"]??"");
           $filter_city=htmlspecialchars($_POST["filter_city"]??"");
           $salary_limit=htmlspecialchars($_POST["salary_range"]??"");
           $salary_mode=htmlspecialchars($_POST["salary_mode"]??"descending");
           $position=htmlspecialchars($_POST["filter_position"]??"");

           $_SESSION["filter"]["filter_category"]=$filter_category;
           $_SESSION["filter"]["filter_city"]=$filter_city;
           $_SESSION["filter"]["salary_limit"]=$salary_limit;
           $_SESSION["filter"]["salary_mode"]=$salary_mode;
           $_SESSION["filter"]["filter_position"]=$position;



           if(!empty($filter_category)){
               $filter_sql.=' AND category=?';
               $parameterek[]=$filter_category;
               $tipusok.='s';
           }
           if($position!=''){
               for($i=0;$i<count($positions);$i++){
                   if($i==$_POST["filter_position"]){
                       $filter_position=$positions[$i];
                   }
               }
               $filter_sql.=' AND position=?';
               $parameterek[]=$filter_position;
               $tipusok.='s';
           }

           if($filter_city!=""){
               for($i=0;$i<count($cities);$i++){
                   if($i==$_POST["filter_city"]){
                       $filter_city=$cities[$i];
                   }
               }
               $filter_sql.=' AND city=?';
               $parameterek[]=$filter_city;
               $tipusok.='s';
           }
           if(!empty($salary_limit)){
               if(isset($_POST["salary_mode"]) and $_POST["salary_mode"]==="ascending"){
                   $filter_sql.=" AND salary <=?";
               }
               else{
                   $filter_sql.=' AND salary <=?';
               }
               $parameterek[]=$salary_limit;
               $tipusok.="d";
           }




           $rendezes=($salary_mode==="ascending")?"ASC":"DESC";

           $filter_sql.=" ORDER BY salary $rendezes";
           $filtered_joblist=$jobs->jobInformation($filter_sql,$parameterek,$tipusok);
           $joblist=$filtered_joblist;
       }
       if(empty($joblist)){
           echo "<h1>Nincsen allas hirdetve ezeken a szuresen</h1>";
       }
        $application=new Application($db);
       if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_apply'])) {
        if($application->job_apply($_SESSION["user_id"],$_POST["allas_id"])){
            echo "<h1 class='apply_successful'>Sikeresen jelentkezve az állásra!</h1>";
        }
        else{
            echo "<h1 class='apply_failed'>Jelentkezés sikertelen!</h1>";
        }
       }

       foreach ($joblist as $job) {
           $title = $job[1];
           $company_name = $job[2];
           $position = $job[3];
           $category_name = $job[4];
           $city = $job[5];
           $salary = $job[6];
           $description = $job[7];
           $created_by = $job[8];

           $status=$application->checkApply($_SESSION["user_id"],$job[0]);

           echo "
            <div class='job_listed'>
                <h1>$title</h1>";

            echo "<h4>Státusz: ";
           if(empty($status)){
               echo "<a>Nincs jelentkezve</a>";
           }
           else if($status["status"]=="pending"){
               echo "<a id='application_pending'>Függőben lévő</a>";

           }
           else if($status["status"]=="accepted"){
               echo " <ap id='application_accepted'>Elfogadott</ap>";

           }
           else if($status["status"]=="rejected"){
               echo "<a id='application_rejected'>Elutasított</a>";

           }




           echo "</h4>";

           $listed_job="
        <p><b>Pozíció: </b> $position</p>
            <details>
                <summary>Részletek</summary>
                <div>
                <p style='font-size: 20px'><b>Cég neve:</b> $company_name</p>
                <p><b>Kategória:</b>";

                   $job_1= "</p>
                <p><b>Város: </b>$city</p>
                <p><b>Fizetés: </b>$salary euro</p>
                <p><b>Leírás: </b>$description</p>
                <p class='posted_by'><b>Posztolta:</b>$created_by</p>
           </div>
       
            </details>
        <form method='post'>
            <input type='hidden' name='allas_id' value='$job[0]'>
            <input type='submit' name='job_apply' ";

        $job_2="</button>
        </form>


        </div>
    ";
           echo $listed_job;
           if (isset($categories_full[$category_name])) {
               echo " ".htmlspecialchars($categories_full[$category_name]) . "</p>";
           }
            echo $job_1;
           if(empty($status)){
               echo "value='Jelentkezés'>";
           }
            else{
                echo "disabled value='Jelentkezés'>";
            }
           echo $job_2;

       }

       ?>
    </div>
    <div class="filter">
            <h2>Szűrő</h2>
        <form method="post">
            <label  for="filter_category">Kategória:</label>
            <select name="filter_category" id="filter_category">
                <option value=''></option>
                <?php

                foreach($categories_full as $id=>$category){
                    if(in_array($id,$categories)){
                        echo "<option value='$id'";
                        if(isset($_SESSION['filter']['filter_category']) and $_SESSION["filter"]["filter_category"]== "$id") {
                            echo 'selected';
                        }

                        }
                        echo ">$category</option>";
                    }


                echo "</select><label  for='filter_city'>Város:</label>
                        <select name='filter_city' id='filter_city'>
                <option value=''></option>";
                $city_id=0;
                foreach($cities as $city){
                    echo "<option value='$city_id'";
                    if(isset($_SESSION['filter']['filter_city']) and $_SESSION['filter']['filter_city']== "$city_id") {
                        echo 'selected';
                    }
                    echo ">$city</option>";
                    $city_id++;
                }

                echo "</select><label  for='filter_position'>Pozíciók:</label>
                        <select name='filter_position' id='filter_position'>
                <option value=''></option>";
                $position_id=0;
                foreach($positions as $position){
                    echo "<option value='$position_id'";
                    if(isset($_SESSION['filter']['filter_position']) and $_SESSION['filter']['filter_position']== "$position_id") {
                        echo 'selected';
                    }
                    echo ">$position</option>";
                    $position_id++;
                }

                ?>

            </select>

            <fieldset>
                <legend>Fizetés rendezés</legend>

                <label for="salary_mode1">
            <input type="radio" id="salary_mode1" name="salary_mode" value='descending' <?php if(isset($_SESSION["filter"]["salary_mode"]) and $_SESSION["filter"]["salary_mode"]=="descending"){echo "checked";}?>>
            Csökkenő
                <label for="salary_mode1">
            <input type="radio" id="salary_mode2" name="salary_mode" value="ascending" <?php if(isset($_SESSION["filter"]["salary_mode"]) and $_SESSION["filter"]["salary_mode"]=="ascending"){echo "checked";}?>>
            Növekvő</label>
                    <p>(alapesetben csökkenő)</p>
            </fieldset>


        <label for="salary_range" id="salary_label">Fizetés(maximum):<span id="max_salary_value_filter"></span></label>

        <input type="range" name="salary_range" id="salary_range" min="0" max="<?=$max_salary?>" value=<?php echo $_SESSION["filter"]["salary_limit"]??0     ?> step="10">
        <input type="number"  name="max_salary_value_number_input" id="salary_range_number_input" value=<?php echo $_SESSION["filter"]["salary_limit"]??"" ?>>
            <Br>
        <input type="submit" name="filter_submit" value="Szűrés">
            <input type="submit" name="clear_filter" value="Szűrő törlése">
        </form>
    </div>
   </div>
</body>
</html>
<script>
    const slider=document.getElementById("salary_range");
    const salary_max=document.getElementById('max_salary_value_filter');
    const input_salary=document.getElementById('salary_range_number_input');

    salary_max.textContent=slider.value;



    function updateAll(value){
        value=parseInt(value)||0;
        if(value<slider.min){
            value=slider.min;
        }
        if(value>slider.max){
            value=slider.max;
        }
        slider.value=value
        salary_max.textContent=value
        input_salary.value=value;
    }

    slider.addEventListener("input",()=>{
            updateAll(slider.value);

        });
    input_salary.addEventListener("input",()=>{
        updateAll(input_salary.value);

    });



</script>