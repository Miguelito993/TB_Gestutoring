<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    //header('Location: index.php');
    //exit();
}
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
            <h2 class="text-center"></h2> 


            <div id="calendar"></div>


        </div> <!-- /container -->   

        <!-- Modal -->
        <?php
        include './inc/modal_event.php';
        ?>
        <!-- /Modal -->

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>

        <script src="./assets/js/fullcalendar.min.js"></script>
        <script src="./assets/js/locale-all.js"></script>
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script src="./assets/js/calendar.js"></script>

        <script type="text/javascript">

            function checkTabHoursByDay(tab, dateNow) {
                const tabDefault = [null, null, null, null, null, null, null, null, false, false, false, false, false, false, false, false, false, false, false, false, false];

                for (var i = 0; i < tab.length; i++) {
                    var year = new Date(tab[i].start).getFullYear();
                    var month = moment(tab[i].start).format('MM');
                    var day = moment(tab[i].start).format('DD');
                    var dateRef = year + "-" + month + "-" + day;
                    var hourRef = new Date(tab[i].start).getHours();
                    var minuteRef = new Date(tab[i].start).getMinutes();
                    if (dateRef == dateNow) {
                        tabDefault[hourRef] = {'0': (minuteRef == '0') ? true : false, '30': (minuteRef == '30') ? true : false};
                    }
                }
                return tabDefault;
            }

            var date = new Date();
            //date.setUTCHours(date.getUTCHours() + 2);

            var myID = "<?php echo $_SESSION['_id'] ?>";

            tabEvents = getPlanningWithIdCoachAndDate(tabEvents, myID, date.toISOString(), true);
            
            jQuery(document).ready(function ($) {
                $('#calendar').fullCalendar({
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
                    dayClick: function (date, jsEvent, view) {
                        if (date.isBefore(moment().subtract(1, 'days'))) {
                            $('#calendar').fullCalendar('unselect');
                            alert('Date non sélectionnable');
                            return false;
                        } else {
                            $('#myEvent').modal('show');
                            $('#inputDate').val(date.format());
                            $('#idCoach').val(myID);

                            var tabHours = checkTabHoursByDay(tabEvents, date.format());
                            var decalage = false;

                            if ($('#inputHeure').val() != null) {
                                $('#inputHeure').html('');
                            }
                            
                            for (var i = 0; i < tabHours.length; i++) {
                                if (tabHours[i] != null) {                                    
                                    if (tabHours[i] != false) {
                                        if (tabHours[i][0] == true) {
                                            $('#inputHeure').append("<option disabled>" + i + "h00</option>");
                                            $('#inputHeure').append("<option disabled>" + i + "h30</option>");
                                        } else {
                                            $('#inputHeure').append("<option>" + i + "h00</option>");
                                            $('#inputHeure').append("<option disabled>" + i + "h30</option>");
                                            $('#inputHeure').append("<option disabled>" + (i + 1) + "h00</option>");
                                            decalage = true; // On créer cette variable pour éviter de mettre deux fois la même heure
                                        }
                                    } else {
                                        if (!decalage) {
                                            $('#inputHeure').append("<option>" + i + "h00</option>");
                                        } else {
                                            decalage = false;
                                        }
                                        $('#inputHeure').append("<option>" + i + "h30</option>");
                                    }
                                }
                            }

                        }
                    },
                    eventClick: function (calEvent, jsEvent, view) {
                        // TODO: Possiblité de modifier les informations d'un événement                    
                        console.log(calEvent);
                        console.log(jsEvent);
                        console.log(view);

                        $(this).css('border-color', 'red');
                    }
                });
                
                $('#calendar').fullCalendar('refetchEvents');
            });
        </script>
    </body>
</html>

