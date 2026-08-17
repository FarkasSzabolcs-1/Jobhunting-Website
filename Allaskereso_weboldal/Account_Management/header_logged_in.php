<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    unset($_SESSION['username']);
    $_SESSION=[];
    session_destroy();
    if($current_page=="index"){
        header("Location: index.php");
        exit;
    }
    else{
        header("Location: ../index.php");
        exit;
    }

}
?>


