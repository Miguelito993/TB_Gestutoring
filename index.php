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
               
            <span id="IronMan_Je" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Je</span> 
            <span id="IronMan_Ve" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Ve</span> 
            <span id="IronMan_Sa" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Di</span> 
            <span id="IronMan_Di" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Lu</span>
            <span id="IronMan_Lu" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Ma</span> 
            <span id="IronMan_Ma" data-container="body" data-toggle="pop_over" data-trigger="hover" data-placement="bottom">Me</span> 
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
        <script type="text/javascript">
            var subject = [];

            // Rempli le formulaire des matières
            $.getJSON(
                    'http://localhost:4242/getMatieres',
                    function (data) {
                        $.each(data, function (index, d) {
                            subject.push(d['name']);
                        });
                    }
            );

            $('#inputSearch').autocomplete({
                source: subject
            });
            
            $('[data-toggle="pop_over"]').popover({html: true, content: "Un <strong>exemple</strong>"});
            
        </script>
    </body>
</html>

