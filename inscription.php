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

            <form method="post" id="inscripForm" class="form-horizontal" enctype="multipart/form-data">
                <h2 class="text-center">Inscription</h2> 

                <div id="alertPopUp" role="alert">
                </div>   

                <div class="form-group">
                    <label for="inputFirstname" class="col-sm-2 control-label">Prénom</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputFirstname" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Nom</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputName" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">E-mail</label>
                    <div class="col-sm-8">
                        <input type="email" class="form-control" id="inputEmail" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPseudo" class="col-sm-2 control-label">Pseudo</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputPseudo" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Mot de passe</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" id="inputPassword" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword2" class="col-sm-2 control-label">Confirmer mot de passe</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" id="inputPassword2" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputCity" class="col-sm-2 control-label">Canton</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="inputCity" required>
                            <!-- Formulaire rempli à l'aide de JQuery -->
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputType" class="col-sm-2 control-label">Type de compte</label>
                    <label class="radio-inline">
                        <input type="radio" name="inputType" value="Student" checked> Étudiant
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="inputType" value="Coach"> Répétiteur
                    </label>
                </div>

                <!-- Champ facultatif selon le type de compte -->

                <div id="divEmailParent" class="form-group">
                    <label for="inputEmailParent" class="col-sm-2 control-label">E-mail d'un parent</label>
                    <div class="col-sm-8">
                        <input type="email" class="form-control" id="inputEmailParent" required>
                    </div>
                </div>

                <div id="divTarif" class="form-group" hidden>
                    <label for="inputTarif" class="col-sm-2 control-label">Tarif</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control" id="inputTarif" step="1" min="1" max="50">
                    </div>
                    <label class="control-label">CHF/Heure</label>
                </div>

                <div id="divMatiere" class="form-group" hidden>
                    <label for="inputMatiere" class="col-sm-2 control-label">Matières d'enseignements <span id="infoPopOver" class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="popover" title="Info" data-trigger="hover" data-content="Veuillez utiliser la touche 'Ctrl' pour sélectionner plusieurs matières"></span></label>
                    <div class="col-sm-8">
                        <select multiple class="form-control" id="inputMatiere">

                        </select>
                    </div>                    
                </div>

                <div id="divDiplome" class="form-group" hidden>
                    <label for="inputDiplome" class="col-sm-2 control-label">Diplômes</label>
                    <div class="col-sm-8">
                        <input type="file" accept='.pdf' id="inputDiplome" multiple>
                    </div>
                </div>

                <!-- end Champ facultatif -->

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" id="submitInscription" class="btn btn-primary" value="Inscription"/>
                    </div>
                </div>
            </form>

        </div> <!-- /container -->       



        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script>
            function StudentAccount() {
                $('#divEmailParent').show();
                $('#divEmailParent input').attr('required', 'true');

                //----------------------------------------------------
                $('#divTarif').hide();
                $('#divTarif input').removeAttr('required');
                $('#divTarif input').val("");

                $('#divMatiere').hide();
                $('#divMatiere select').removeAttr('required');
                $('#divMatiere select').val("");

                $('#divDiplome').hide();
                $('#divDiplome input').removeAttr('required');
                $('#divDiplome input').val("");
                //----------------------------------------------------              
            }

            function CoachAccount() {
                $('#divTarif').show();
                $('#divTarif input').attr('required', 'true');

                $('#divMatiere').show();
                $('#divMatiere select').attr('required', 'true');

                $('#divDiplome').show();
                $('#divDiplome input').attr('required', 'true');

                //----------------------------------------------------
                $('#divEmailParent').hide();
                $('#divEmailParent input').removeAttr('required');
                $('#divEmailParent input').val("");
                //----------------------------------------------------                
            }

            $('input[name=inputType]').change(function () {
                var typeValue = $('input[name=inputType]:checked').val();

                if (typeValue === "Student") {
                    StudentAccount();
                } else {
                    CoachAccount();
                }
            });

            jQuery(document).ready(function ($) {

                $('[data-toggle="popover"]').popover();

                // Rempli le formulaire des cantons
                $.getJSON(
                        'http://localhost:4242/getDepartments',
                        function (data) {
                            $.each(data, function (index, d) {
                                $('#inputCity').append("<option>" + d['name'] + "</option>");
                            });
                        }
                );

                // Rempli le formulaire des matières
                $.getJSON(
                        'http://localhost:4242/getMatieres',
                        function (data) {
                            $.each(data, function (index, d) {
                                $('#inputMatiere').append("<option>" + d['name'] + "</option>");
                            });
                        }
                );

                $('#inscripForm').submit(function (e) {
                    // On désactive le comportement par défaut du navigateur
                    e.preventDefault();

                    $.post(
                        'http://localhost:4242/submitInscription',
                        {
                            firstname: $('#inputFirstname').val(),
                            name: $('#inputName').val(),
                            email: $('#inputEmail').val(),
                            pseudo: $('#inputPseudo').val(),
                            pwd: $('#inputPassword').val(),
                            city: $('#inputCity').val(),
                            soldes: 0,
                            type: $('input[name=inputType]:checked').val(),
                            isOnline: false,
                        
                            diplomes: $('#inputDiplome').val(),
                            tarif: $('#inputTarif').val(),
                            isValid: false,
                            matieres: $('#inputMatiere').val()
                        }
                    ,
                        function (data) {
                            console.log(data);
                        }
                    );

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

