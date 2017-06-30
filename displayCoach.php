<?php
session_start();

if (!isset($_POST['submitSearch'])) {
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
        <link rel='stylesheet' href="./assets/css/fullcalendar.min.css">
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
            <div id="alertPopUp" role="alert"></div>
            <h2 class="text-center">Résultats pour: <?php echo $matiere; ?></h2> 


        </div> <!-- /container -->   

        <!-- Modal -->
        <?php
        include './inc/modal_inscrip.php';
        include './inc/modal_calendar.php';
        ?>
        <!-- /Modal -->

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>

        <script src="./assets/js/fullcalendar.min.js"></script>
        <script src="./assets/js/locale-all.js"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script src="./assets/js/connexion.js"></script>
        <script src="./assets/js/inscription.js"></script>
        <script src="./assets/js/calendar.js"></script>

        <script type="text/javascript">
            var green_circle = "./assets/img/green_circle.svg";
            var red_circle = "./assets/img/red_circle.svg";

            var promiseOfPlanning;

            var tabPlanning, dateLoop = null;
            var week = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];

            var date = new Date();
            //date.setUTCHours(date.getUTCHours() + 2);

            var dateLimitRef = new Date(date);
            dateLimitRef.setDate(date.getDate() + 6);

            dateLoop = new Date(date);

            // Recupère les coachs
            $.getJSON(
                'http://localhost:4242/getCoaches/<?php echo $matiere; ?>',
                function (data) {
                    //console.log(data); 
                    $.each(data, function (index, d) {
                        var elemCoach = $('<div></div>').addClass('listCoach media container').attr('id', d['pseudo']);
                        var elemDivImage = $('<div></div>').addClass('media-left media-middle');
                        var elemImage = $('<img>').addClass('media-object').attr('src', './memoire_tb_pereira/img/draft.png').attr('alt', 'Image de profil').attr('height', 128).attr('width', 128);
                        var elemDivBody = $('<div></div>').addClass('media-body');
                        elemDivImage.append(elemImage);
                        elemDivBody.append('<input type="text" value="' + d['_id'] + '" hidden/>');
                        elemDivBody.append('<h4>' + d['prenom'] + ' ' + d['nom'] + '</h4><br/>');
                        elemDivBody.append('<span>Matières: ' + d['matieres'] + '</span><br/>');
                        elemDivBody.append('<span>Canton: ' + d['canton'] + '</span><br/>');
                        elemDivBody.append('Statut: <img src="' + ((d['isOnline'] == true) ? green_circle : red_circle) + '" alt="Statut connexion" height="20" width="20"><br/>');
                        elemDivBody.append('<span>Tarif: ' + d['tarif'] + ' CHF/Heure</span><br/>');
                        elemDivBody.append('<button name="rsv" type="button" class="btn btn-primary" <?php echo ((!isset($_SESSION['_id']) || $_SESSION['type'] == 'Coach') ? 'disabled="disabled"' : ''); ?>>Prendre rendez-vous</button><br/>');

                        $.post('http://localhost:4242/getNotation', {
                            id_user: d['_id']
                        },
                            function (dataNotation) {
                                elemDivBody.append('<span>Note: ' + ((dataNotation == null) ? 'Pas de notes' : '<strong>' + dataNotation + '</strong>') + '</span><br/>');
                            }
                        );

                        $.post('http://localhost:4242/getPlanning', {
                            id_coach: d['_id'],
                            dateLimit: dateLimitRef.toISOString(),
                            dateNow: date.toISOString()
                        },
                            function (dataPlanning) {
                                const tabDefault = [null, null, null, null, null, null, null, null, false, false, false, false, false, false, false, false, false, false, false, false, false];
                                var isDayFree = false;

                                for (var i = 0; i < 7; i++) {
                                    var tmpTab = tabDefault;
                                    for (var j = 0; j < dataPlanning.length; j++) {
                                        var tmpDate = new Date(dataPlanning[j].date);
                                        if (dateLoop.getDay() === tmpDate.getDay()) {
                                            if (dataPlanning[j].isFree) {
                                                tmpTab[tmpDate.getUTCHours()] = dataPlanning[j].isFree;
                                                isDayFree = true;
                                            }
                                        }
                                    }
                                    //console.log(tmpTab);
                                    elemDivBody.append('<span id="' + d['pseudo'] + '_' + week[dateLoop.getDay()].substring(0, 2) + '" data-container="body" data-toggle="overpop" data-trigger="hover" data-placement="bottom" style="color: ' + ((isDayFree) ? 'green' : 'red') + '">' + week[dateLoop.getDay()].substring(0, 2) + '</span> ');
                                    dateLoop.setDate(dateLoop.getDate() + 1);
                                    isDayFree = false;
                                }

                                elemDivBody.append('<br/>');
                            }
                        );

                        elemCoach.append(elemDivImage);
                        elemCoach.append(elemDivBody);
                        $('#divContainer').append(elemCoach);
                    });
                }
            );

            $('[data-toggle="overpop"]').popover({html: true, content: "Un <strong>exemple</strong>"});


            jQuery(document).ready(function ($) {

                $('#rsvCalendar').fullCalendar({
                    locale: 'fr',
                    // enable theme
                    theme: true,
                    // emphasizes business hours
                    businessHours: true,
                    // header
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    selectable: true,

                    eventLimit: true,

                    eventSources: [],

                    eventClick: function (calEvent, jsEvent, view) {                        
                        if (calEvent.editable) {
                            // TODO: Vérifier avec une alerte si l'utilisateur veut vraiment réservé cette période
                            var idMeeting = calEvent.id;
                            var idStudent = '<?php echo $_SESSION['_id']; ?>';
                            $.get(
                                'http://localhost:4242/getMatiereIDByName/' + <?php echo $matiere; ?>,
                                function (mat) {
                                    $.post('http://localhost:4242/makeMeeting', {
                                        idMeeting: idMeeting,
                                        idStudent: idStudent,
                                        idMatiere: mat[0]._id
                                    },
                                        function (data) {
                                            console.log(data);
                                        }
                                    );
                                }
                            );
                        }

                        $(this).css('border-color', 'red');
                    }
                });

                $('[name="rsv"]').click(function (e) {
                    var idCoach = $(this)[0].parentNode.childNodes[0].value;
                    var name = $(this)[0].parentNode.childNodes[1];
                    var fullName = name.outerHTML.substr(4, name.outerHTML.length - 9);

                    promiseOfPlanning = $.post('http://localhost:4242/getPlanning', {
                        id_coach: idCoach,
                        dateNow: date.toISOString()
                    },
                        function (data) {
                            $.each(data, function (index, d) {
                                $.get(
                                    'http://localhost:4242/getPseudoById/' + d['id_student'],
                                    function (user) {
                                        var myTitle = (d['isFree'] == true) ? "Libre" : "Occupé";
                                        var myStart = new Date(d['date']);
                                        var myEnd = transformDateStartToEnd(myStart, d['duration']);
                                        var myColor = ((myTitle == "Libre") ? "#1E9C1E" : "#FF0000");
                                        var isEditable = (d['isFree'] == true) ? true : false;
                                        tabEvents.push({id: d['_id'], title: myTitle, start: myStart, end: myEnd, color: myColor, editable: isEditable});
                                    }
                                );
                            });
                        }
                    );

                    promiseOfPlanning.then(function () {
                        console.log(tabEvents);
                        $('#rsvCalendar').fullCalendar('removeEvents');
                        $('#rsvCalendar').fullCalendar('addEventSource', tabEvents);
                        $('#rsvCalendar').fullCalendar('refetchEvents');
                        $('#pseudoText').text(' - ' + fullName);
                        $('#myCalendar').modal('show');
                    });

                });

                $('#myCalendar').on('hide.bs.modal', function (e) {
                    tabEvents = [];
                    $('#rsvCalendar').fullCalendar('removeEvents');
                });
            });
        </script>
    </body>
</html>

