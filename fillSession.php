<?php
    session_start();

    if(isset($_POST['myUser'])){
        foreach ($_POST['myUser'] as $key => $value){
            $_SESSION[$key] = $value;
        }    
    }
?>