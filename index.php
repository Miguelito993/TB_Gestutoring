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
        <link rel="icon" href="./assets/img/logo_GesTutoring.ico">
        <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="./bootstrap/css/bootstrap-theme.css">
        <link rel="stylesheet" href="./bootstrap/css/theme.css">
        <link rel="stylesheet" href="./bootstrap/css/signin.css">
        <link rel="stylesheet" href="./assets/css/session.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    </head>
    <body>

        <!-- Fixed navbar -->        
        <nav id="navMenu" class="navbar navbar-inverse navbar-fixed-top">
            <?php
            include './inc/inc_navigation.php';
            ?>
        </nav>
        <!-- End Fixed navbar -->

        <div class="container-fluid">
            <div id="alertPopUp" role="alert"></div>
            <h2 class="text-center">Recherchez des répétiteurs:</h2> 
            <form method="post" id="searchCoach" class="form-horizontal" action="displayCoach.php">               
                <div class="form-group">
                    <label for="inputSearch" class="col-sm-2 control-label">Thème:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputSearch" name="inputSearch" placeholder="Votre matière" required>
                    </div>
                    <input type="submit" id="submitSearch" name="submitSearch" class="btn btn-primary" value="Rechercher"/>
                </div>
            </form>            


        </div> <!-- /container -->

        <!-- Modal -->
        <?php
        include './inc/modal_inscrip.php';
        include './inc/modal_listUser.php';
        include './inc/modal_validUser.php';
        ?>
        <!-- /Modal -->

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script src="./assets/js/connexion.js"></script>
        <script src="./assets/js/inscription.js"></script>
        <script src="./assets/js/sha1.js"></script>
        <script src="./assets/js/session.js"></script>
        <script type="text/javascript">
              var subject = [];

              var matiere;
              var pseudoPartner;

              // Rempli le formulaire des matières
              $.getJSON(
                'http://localhost:4242/getMatieres',
                function (data) {
                    $.each(data, function (index, d) {
                        subject.push(d['name']);
                    });
                }
              );

              // TODO: Mettre en place des informations pour remplir la page, tant pour les utilisateurs connecté que pour les déconnectés
              /*
               $.get(
               'http://localhost:4242/getMatiereIDByName/Géographie',
               function (mat) {
               console.log(mat[0]._id);
               }
               );
               
               
               
               
               
               $.get(
               'http://localhost:4242/getFile/img/Thor.png',
               function (file) {
               console.log(file);
               $('#imgTest').attr('src', file);
               }
               );
               
               
               $.get(                
               'http://localhost:4242/getDataList/Anglais/593fcce24f53f02754cc352c',
               function (data) {  
               //console.log(data);
               
               
               //var dataBlob = new Blob([data], {type: 'text/plain'});
               //var dataFile = new File([data], "exercice.pdf", {type: "application/pdf", lastModified: Date.now()});
               //console.log(dataBlob);
               //console.log(dataFile);
               //urlBlob = window.URL.createObjectURL(dataBlob);
               //urlFile = window.URL.createObjectURL(dataFile);
               //console.log(urlBlob);           
               //console.log(urlFile);   
               
               }
               ); 
               */

              $('#inputSearch').autocomplete({
                  source: subject
              });

              jQuery(document).ready(function ($) {
                  // ===========================================================
                  // Méthode jQuery pour la validation d'un utilisateur                  
                  $('#linkValidation').click(function (e) {
                      e.preventDefault();

                      $.getJSON(
                        'http://localhost:4242/getUserInactif',
                        function (data) {
                            $.each(data, function (index, d) {
                                $('#listUser').append('<li><button type="button" id="' + d['_id'] + '" class="btn btn-default" name="validUser">' + d['pseudo'] + '</button></li>');
                            });
                            $('#myListUser').modal('show');
                        }
                      );
                  });

                  $('#myListUser').on('hide.bs.modal', function () {
                      $('#listUser').empty();
                  });
                  $('#listUser').on('click', '[name="validUser"]', function () {
                      $.getJSON(
                        'http://localhost:4242/getUserById/' + $(this)[0].id,
                        function (data) {
                            var diplomes = '';
                            $('#vu_imgProfil').attr('src', 'http://localhost:4242/getFile/img/' + data[0].img_profil);
                            $('#vu_inputFirstname').text(data[0].prenom);
                            $('#vu_inputName').text(data[0].nom);
                            $('#vu_inputEmail').text(data[0].email);
                            $('#vu_inputPseudo').text(data[0].pseudo);
                            $('#vu_inputCity').text(data[0].canton);
                            $('#vu_inputTarif').text(data[0].tarif);
                            $('#vu_inputMatiere').text(data[0].matieres);
                            for (var i = 0; i < data[0].diplomes.length; i++) {
                                diplomes += '<a href="http://localhost:4242/getFile/uploads/' + data[0].diplomes[i] + '" target="_blank">' + data[0].diplomes[i] + '</a><br/>';
                            }
                            $('#vu_cellDiplomes').append($(diplomes));
                            $('#submitValidUser').attr('name', data[0]._id);

                            $('#myValidUser').modal('show');
                        }
                      );
                  });
                  $('#myValidUser').on('hide.bs.modal', function () {
                      $('#vu_cellDiplomes').empty();
                  });
                  $('#submitValidUser').click(function (e) {
                      e.preventDefault();

                      $.post('http://localhost:4242/validUser', {
                          id: $('#submitValidUser')[0].name
                      },
                        function (data) {
                            console.log(data);
                            $('#myValidUser').modal('hide');
                            $('#myListUser').modal('hide');

                            $("#alertPopUp").attr('class', 'alert alert-success alert-dismissible');
                            $("#alertPopUp").empty();
                            $("#alertPopUp").append("Validation effectué avec succès");
                        }
                      );

                  });
                  // ===========================================================
              });

        </script>
    </body>
</html>

