<?php
session_start();

if(!isset($_POST['submitSearch'])){
    header('Location: index.php');
    exit();
}
$matiere = $_POST['inputSearch'];
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
        <nav id="navMenu" class="navbar navbar-inverse navbar-fixed-top">
            <?php
            include './inc/inc_navigation.php';
            ?>
        </nav>
        <!-- End Fixed navbar -->

        <div id="divContainer" class="container">
            <h2 class="text-center">Résultats pour: <?php echo $matiere; ?></h2> 
                        

        </div> <!-- /container -->   
        
        <!-- Modal -->
        <?php
            include './inc/modal_inscrip.php';
        ?>
        <!-- /Modal -->

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script src="./assets/js/connexion.js"></script>
        <script src="./assets/js/inscription.js"></script>
        <script>                        
            // Recupère les coachs
            $.getJSON(
                    'http://localhost:4242/getCoaches/<?php echo $matiere;?>',                   
                    function (data) {
                        $.each(data, function (index, d) {
                            var elemCoach = $('<div></div>').addClass('center-block').attr('id', d['pseudo']);
                            elemCoach.append(''+d['prenom']+' '+d['nom']+'');
                            $('#divContainer').append(elemCoach);
                        });
                    }
            ); 
            
/*
            jQuery(document).ready(function ($) {
            
            
            });
*/
        </script>
    </body>
</html>

