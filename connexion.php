<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Plate-forme d'e-learning</title>
        <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="./bootstrap/css/bootstrap-theme.css">
        <link rel="stylesheet" href="./bootstrap/css/theme.css">
        <link rel="stylesheet" href="./bootstrap/css/signin.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    </head>
    <body>

        <!-- Fixed navbar -->
        <?php
        include './inc/inc_navigation.php';
        ?>
        <!-- End Fixed navbar -->

        <div class="container">
            <?php
            if (!isset($_SESSION['user'])) {
                if (isset($_SESSION['msgInscription'])) {
                    if ($_SESSION['msgInscription'] == 'Success') {
                        echo '<div id="alertPopUp" class="alert alert-success" role="alert">
                                        L\'utilisateur a été créé avec succés
                                    </div>';
                    } elseif ($_SESSION['msgInscription'] == 'LoginExist') {
                        echo '<div id="alertPopUp" class="alert alert-danger" role="alert">
                                        Ce login est déjà existant dans notre système
                                    </div>';
                    } else {
                        echo '<div id="alertPopUp" class="alert alert-danger" role="alert">
                                        Veuillez renseigner les champs
                                    </div>';
                    }
                    unset($_SESSION['msgInscription']);
                } else {
                    echo '<div id="alertPopUp" role="alert">
                                </div>';
                }
            } else {
                echo '<h1 class="display-3">Bienvenue sur mon site, veuillez utilisez les menus pour naviguer</h1>';
            }
            ?>

            <form method="post" id="connexForm" class="form-horizontal">
                <h2 class="text-center">Connexion</h2> 

                <div id="alertPopUp" role="alert">
                </div>   

                <div class="form-group">
                    <label for="inputPseudo" class="col-sm-2 control-label">Pseudo</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputPseudo" placeholder="Votre pseudo" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Mot de passe</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" id="inputPassword" placeholder="Votre mot de passe" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" id="submitConnexion" class="btn btn-primary" value="Connexion"/>
                    </div>
                </div>
            </form>

        </div> <!-- /container -->


        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script>
            jQuery(document).ready(function ($) {
                $('#connexForm').submit(function (e) {
                    // On désactive le comportement par défaut du navigateur
                    e.preventDefault();
                    
                    var pseudo = $('#inputPseudo').val();
                    var pwd = $('#inputPassword').val();

                    $.post(
                            'http://localhost:4242/checkLogin/'+pseudo+'/'+pwd+'/',
                            null,
                            function(data){
                                window.alert(data);
                            }
                    );

                    //TODO: Vérifier que les logins ne sont pas vides et envoyer au serveur les informations
                    /*        
                     // On envoi la requête AJAX                    
                     $.post(
                     'connexion.php',
                     {
                     login: $('#inputLogin').val(),
                     password: $('#inputPassword').val()
                     },
                     
                     function(data){                            
                     if(data == 'Success'){
                     // Changé la page
                     window.location.replace("index.php");
                     }else if (data == 'Failed'){
                     $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');                               
                     $("#alertPopUp").empty();
                     $("#alertPopUp").append("Login/Password erroné");
                     }else if (data == 'Empty'){
                     $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');
                     $("#alertPopUp").empty();
                     $("#alertPopUp").append("Champ vide");
                     }
                     }
                     );
                     */
                });
            });

        </script>
    </body>
</html>

