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
            var green_circle = "./assets/img/green_circle.svg";
            var red_circle = "./assets/img/red_circle.svg";
    
            // Recupère les coachs
            $.getJSON(
                    'http://localhost:4242/getCoaches/<?php echo $matiere;?>',                   
                    function (data) {
                        $.each(data, function (index, d) {
                            var elemCoach = $('<div></div>').addClass('listCoach media').attr('id', d['pseudo']);
                            var elemDivImage = $('<div></div>').addClass('media-left media-middle');
                            var elemImage = $('<img>').addClass('media-object').attr('src','./memoire_tb_pereira/img/draft.png').attr('alt','Image de profil').attr('height',128).attr('width',128);
                            var elemDivBody = $('<div></div>').addClass('media-body');
                            elemDivImage.append(elemImage);
                            elemDivBody.append('<h4>'+d['prenom']+' '+d['nom']+'</h4><br/>');
                            elemDivBody.append('<span>Matières: '+d['matieres']+'</span><br/>');
                            elemDivBody.append('<span>Canton: '+d['canton']+'</span><br/>');
                            elemDivBody.append('Statut: <img src="'+((d['isOnline'] == true)?green_circle:red_circle) +'" alt="Statut connexion" height="20" width="20"><br/>');
                            elemDivBody.append('<span>Tarif: '+d['tarif']+' CHF/Heure</span><br/>');
                            elemDivBody.append('<button type="button" class="btn btn-primary">Prendre rendez-vous</button><br/>');
                            elemCoach.append(elemDivImage);                            
                            elemCoach.append(elemDivBody);
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

