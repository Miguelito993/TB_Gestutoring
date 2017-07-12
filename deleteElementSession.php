<?php
    session_start();

    if(isset($_GET['name_dipl'])){
        if(($key = array_search($_GET['name_dipl'], $_SESSION['diplomes'])) !== false) {
            unset($_SESSION['diplomes'][$key]);
        }
        header('Location: profil.php'); 
    }else{
        header('Location: index.php');  
    }
?>