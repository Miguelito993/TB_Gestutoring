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
            <h2 class="text-center">Recherchez des répétiteurs:</h2> 
           <form method="post" id="searchCoach" class="form-horizontal">               
                                                   
                <div class="form-group">
                  <label for="inputSearch" class="col-sm-2 control-label">Thème:</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="inputSearch" placeholder="Votre matière" required>
                  </div>
                  <input type="submit" id="submitSearch" class="btn btn-primary" value="Rechercher"/>
                </div>                              
               
            </form>
            
        </div> <!-- /container -->    
        
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script>
            var subject = ["Français","Mathématiques","Biologie","Physique", "Éléctronique", "Éducation physique"];
            
            $('#inputSearch').autocomplete({
                source: subject
            });
            
            jQuery(document).ready(function($){
                $('#searchCoach').submit(function(e){
                    // On désactive le comportement par défaut du navigateur
                    e.preventDefault();
                                       
                    window.alert($("#inputSearch").val());
                    
                    $.get(
                            'http://localhost:4242/sous-sol',
                            'false',
                            function(data){
                                console.log(data);
                            },
                            'json'
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
