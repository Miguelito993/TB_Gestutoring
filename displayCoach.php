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
        <link rel="icon" href="./assets/img/logo_GesTutoring.ico">
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
            <div id="alertPopUpCalendar" role="alert"></div>
            <table>
                <tr>
                    <td class="col-xs-2"><a id="linkPrevious"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Revenir à l'accueil</a></td>
                    <td class="col-xs-6"><h2 class="text-center">Résultats pour: <?php echo $matiere; ?></h2></td>
                    <td class="col-xs-2"><select id="filterStatut" name="optionFilter" class="form-control"><option disabled selected>Statut</option><option value="1">Tous</option><option value="2">En ligne</option><option value="3">Hors-ligne</option></select></td>
                    <td class="col-xs-2"><select id="triCoaches" name="optionFilter" class="form-control"><option disabled selected>Filtre</option><option value="1"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>Prix croissant</option><option value="2"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>Prix décroissant</option><option value="3"><span class="glyphicon glyphicon-sort-by-attributes" aria-hidden="true"></span>Note croissante</option><option value="4"><span class="glyphicon glyphicon-sort-by-attributes-alt" aria-hidden="true"></span>Note décroissante</option></select></td>
                </tr>
            </table>


        </div> <!-- /container -->

        <!-- Modal -->
        <?php
        include './inc/modal_inscrip.php';
        include './inc/modal_calendar.php';
        include './inc/modal_addDatas.php';
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
        <script src="./assets/js/sha1.js"></script>
        <script src="./assets/js/addDatas.js"></script>

        <script type="text/javascript">
              var green_circle = "./assets/img/green_circle.svg";
              var red_circle = "./assets/img/red_circle.svg";

              var promiseOfNotation;
              var promiseOfDispo;

              var promiseOfPlanning;

              var tabPlanning, dateLoop = null;
              var week = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];

              var tarifCoach = [];
              var resultRequest;

              var date = new Date();
              //date.setUTCHours(date.getUTCHours() + 2);

              var dateLimitRef = new Date(date);
              dateLimitRef.setDate(date.getDate() + 6);

              dateLoop = new Date(date);

              function transformObjectToArrayHTML(stringList) {
                  var tmp = '';

                  for (var index in stringList) {
                      tmp += (stringList[index] + '<br/>');
                  }

                  return tmp;
              }

              // Recupère les coachs
              $.getJSON(
                'http://localhost:4242/getCoaches/<?php echo $matiere; ?>',
                function (data) {
                    resultRequest = data;

                    $.each(data, function (index, d) {
                        // TODO: Compléter les informations des utilisateurs dans un tableau et l'affiche ensuite.
                        //       Pour pouvoir ensuite utiliser les filtres plus facilement avec JavaScript

                        var elemCoach = $('<div></div>').addClass('listCoach').addClass('table-responsive').attr('id', d['pseudo']);
                        var elemTabBody = $('<table></table').addClass('table');

                        tarifCoach[d['pseudo']] = d['tarif'];

                        var elemImage = $('<img>').addClass('img-responsive').attr('src', 'http://localhost:4242/getFile/img/' + d['img_profil']).attr('alt', 'Image de profil').attr('height', 128).attr('width', 128);
                        var elemName = $('<h4></h4>').text(d['prenom'] + ' ' + d['nom']);
                        var elemMatiere = $('<span>Matières </span>').append($('<span></span>').addClass('glyphicon glyphicon-tags').attr('aria-hidden', 'true').attr('data-toggle', 'popMatiere').attr('data-trigger', 'hover').attr('title', 'Matières enseignées').attr('data-html', 'true').attr('data-content', transformObjectToArrayHTML(d['matieres'])));
                        var elemCanton = $('<span></span>').text('Canton: ' + d['canton']);
                        var elemStatut = $('<span>Statut: <img src="' + ((d['isOnline'] == true) ? green_circle : red_circle) + '" alt="Statut connexion" height="20" width="20"></span>');
                        var elemTarif = $('<h3></h3>').addClass('text-center').text(d['tarif'] + ' CHF/Heure');
                        var elemRDV = $('<button name="rsv" type="button" class="btn btn-primary" <?php echo ((!isset($_SESSION['_id']) || $_SESSION['type'] != 'Student') ? 'disabled="disabled"' : ''); ?>>Prendre rendez-vous</button>');
                        var elemNote = $('<h3></h3>').addClass('text-center');;
                        var elemDispo = $('<span></span>');


                        promiseOfNotation = $.post('http://localhost:4242/getNotation', {
                            id_coach: d['_id']},
                          function (dataNotation) {
                              resultRequest[index].note = ((dataNotation == null) ? null : dataNotation);
                              elemNote.append('Note: ' + ((dataNotation == null) ? 'Pas de notes' : '<strong>' + dataNotation + '/10</strong>'));
                          }
                        );

                        promiseOfDispo = $.post('http://localhost:4242/getPlanning', {
                            id_user: d['_id'],
                            dateLimit: dateLimitRef.toISOString(),
                            dateNow: date.toISOString(),
                            type: 'Coach'
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
                                  elemDispo.append($('<span></span>').attr('id', d['pseudo'] + '_' + (dateLoop.getMonth() + 1) + '-' + dateLoop.getDate() + '-' + dateLoop.getFullYear()).attr('data-container', 'body').attr('data-toggle', 'overpop').attr('data-trigger', 'manual').attr('data-placement', 'bottom').attr('data-content', '_').css('color', ((isDayFree) ? 'green' : 'red')).text(week[dateLoop.getDay()].substring(0, 2) + ' '));

                                  dateLoop.setDate(dateLoop.getDate() + 1);
                                  isDayFree = false;
                              }
                              dateLoop = new Date(date);
                          }
                        );

                        promiseOfNotation.then(function () {
                            return promiseOfDispo;
                        })
                          .then(function () {
                              elemTabBody.append('<tr><td class="col-md-2" rowspan="5">' + elemImage[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemName[0].outerHTML + '</td><td class="col-md-4"><input type="text" value="' + d['_id'] + '" hidden/></td><td class="col-md-2"></td><td class="col-md-2"></td></tr>\n\
<tr><td class="col-md-2">' + elemMatiere[0].outerHTML + '</td><td class="col-md-4">' + elemTarif[0].outerHTML + '</td><td class="col-md-2">Disponibilités:</td><td class="col-md-2" rowspan="2">' + elemRDV[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemCanton[0].outerHTML + '</td><td class="col-md-4">' + elemNote[0].outerHTML + '</td><td class="col-md-2">' + elemDispo[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemStatut[0].outerHTML + '</td><td class="col-md-4"></td><td class="col-md-2"></td><td class="col-md-2"></td></tr>');

                              elemCoach.append(elemTabBody);
                              $('#divContainer').append(elemCoach);

                              $('[data-toggle="popMatiere"]').popover();
                              $('[data-toggle="overpop"]').popover({html: true});

                              $('[data-toggle="overpop"]').each(function () {
                                  // Compare si le texte n'est pas rouge
                                  if ($(this).css("color") == 'rgb(255, 0, 0)') {
                                      $('#' + $(this)[0].id).popover('disable');
                                  }
                              });
                          });
                    });
                }
              );

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
                          right: 'month,agendaWeek,agendaDay'},
                      selectable: true,

                      eventLimit: true,

                      eventSources: [],

                      eventClick: function (calEvent, jsEvent, view) {
                          var pseudoCoach = $('#pseudoText').val();
                          if (calEvent.editable) {
                              if (tarifCoach[pseudoCoach] <= parseInt('<?php echo $_SESSION['soldes']; ?>')) {
                                  if (confirm("Voulez-vous réserver la séance à " + moment(calEvent.start).format("HH:mm") + "?")) {
                                      var idMeeting = calEvent.id;
                                      var idStudent = '<?php
                                                            if (isset($_SESSION['_id'])) {
                                                                echo $_SESSION['_id'];
                                                            }
                                                        ?>';
                                      $.get(
                                        'http://localhost:4242/getMatiereIDByName/<?php echo $matiere; ?>',
                                        function (mat) {
                                            console.log(mat);
                                            $.post('http://localhost:4242/makeMeeting', {
                                                idMeeting: idMeeting,
                                                idStudent: idStudent,
                                                idMatiere: mat[0]._id
                                            },
                                              function (data) {
                                                  console.log(data);
                                                  $('#rsvCalendar').fullCalendar('refetchEvents');

                                                  $('#myCalendar').modal('hide');
                                                  $("#alertPopUpCalendar").attr('class', 'alert alert-success alert-dismissible');
                                                  $("#alertPopUpCalendar").empty();
                                                  $("#alertPopUpCalendar").append("Votre rendez-vous a été marqué pour le " + moment(calEvent.start).format("DD MMMM YYYY") + " à " + moment(calEvent.start).format("HH:mm"));
                                              }
                                            );
                                        }
                                      );
                                  }
                              } else {
                                  alert('Votre soldes ne permet pas de réserver une heure avec ce coach');
                              }
                          }
                      }
                  });

                  $('select[name=optionFilter]').change(function () {
                      var filterChoice = $('#filterStatut option:selected').text();
                      var triChoice = $('#triCoaches option:selected').text();
                      var dataTri = resultRequest;

                      for (var i = 0; i < dataTri.length; i++) {
                          $('#' + dataTri[i].pseudo).remove();
                      }

                      // Trier les répétiteurs
                      switch (triChoice) {
                          case "Prix croissant":
                              dataTri.sort(function (a, b) {
                                  return parseInt(a.tarif) - parseInt(b.tarif);
                              });
                              break;
                          case "Prix décroissant":
                              dataTri.sort(function (a, b) {
                                  return parseInt(a.tarif) + parseInt(b.tarif);
                              });
                              break;
                          case "Note croissante":
                              dataTri.sort(function (a, b) {
                                  return parseFloat(a.note) - parseFloat(b.note);
                              });
                              break;
                          case "Note décroissante":
                              dataTri.sort(function (a, b) {
                                  return parseFloat(a.note) + parseFloat(b.note);
                              });
                              break;
                          default:
                              break;
                      }
                      // Filtrer les répétiteurs
                      switch (filterChoice) {
                          case "En ligne":
                              dataTri = dataTri.filter(function (el) {
                                  return (el.isOnline == true);
                              });
                              break;
                          case "Hors-ligne":
                              dataTri = dataTri.filter(function (el) {
                                  return (el.isOnline == false);
                              });
                              break;
                          default:
                              break;
                      }



                      // Affichage de la nouvelle liste des répétiteurs
                      $.each(dataTri, function (index, d) {

                          var elemCoach = $('<div></div>').addClass('listCoach').addClass('table-responsive').attr('id', d['pseudo']);
                          var elemTabBody = $('<table></table').addClass('table');

                          tarifCoach[d['pseudo']] = d['tarif'];

                          var elemImage = $('<img>').addClass('img-responsive').attr('src', 'http://localhost:4242/getFile/img/' + d['img_profil']).attr('alt', 'Image de profil').attr('height', 128).attr('width', 128);
                          var elemName = $('<h4></h4>').text(d['prenom'] + ' ' + d['nom']);
                          var elemMatiere = $('<span>Matières </span>').append($('<span></span>').addClass('glyphicon glyphicon-tags').attr('aria-hidden', 'true').attr('data-toggle', 'popMatiere').attr('data-trigger', 'hover').attr('title', 'Matières enseignées').attr('data-html', 'true').attr('data-content', transformObjectToArrayHTML(d['matieres'])));
                          var elemCanton = $('<span></span>').text('Canton: ' + d['canton']);
                          var elemStatut = $('<span>Statut: <img src="' + ((d['isOnline'] == true) ? green_circle : red_circle) + '" alt="Statut connexion" height="20" width="20"></span>');
                          var elemTarif = $('<h3></h3>').addClass('text-center').text(d['tarif'] + ' CHF/Heure');
                          var elemRDV = $('<button name="rsv" type="button" class="btn btn-primary" <?php echo ((!isset($_SESSION['_id']) || $_SESSION['type'] != 'Student') ? 'disabled="disabled"' : ''); ?>>Prendre rendez-vous</button>');
                          var elemNote = $('<h3></h3>').addClass('text-center');
                          elemNote.append('Note: ' + ((d['note'] == null) ? 'Pas de notes' : '<strong>' + d['note'] + '/10</strong>'));
                          var elemDispo = $('<span></span>');


                          promiseOfDispo = $.post('http://localhost:4242/getPlanning', {
                              id_user: d['_id'],
                              dateLimit: dateLimitRef.toISOString(),
                              dateNow: date.toISOString(),
                              type: 'Coach'
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
                                    elemDispo.append($('<span></span>').attr('id', d['pseudo'] + '_' + (dateLoop.getMonth() + 1) + '-' + dateLoop.getDate() + '-' + dateLoop.getFullYear()).attr('data-container', 'body').attr('data-toggle', 'overpop').attr('data-trigger', 'manual').attr('data-placement', 'bottom').attr('data-content', '_').css('color', ((isDayFree) ? 'green' : 'red')).text(week[dateLoop.getDay()].substring(0, 2) + ' '));

                                    dateLoop.setDate(dateLoop.getDate() + 1);
                                    isDayFree = false;
                                }
                                dateLoop = new Date(date);
                            }
                          );

                          promiseOfDispo.then(function () {
                              elemTabBody.append('<tr><td class="col-md-2" rowspan="5">' + elemImage[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemName[0].outerHTML + '</td><td class="col-md-4"><input type="text" value="' + d['_id'] + '" hidden/></td><td class="col-md-2"></td><td class="col-md-2"></td></tr>\n\
<tr><td class="col-md-2">' + elemMatiere[0].outerHTML + '</td><td class="col-md-4">' + elemTarif[0].outerHTML + '</td><td class="col-md-2">Disponibilités:</td><td class="col-md-2" rowspan="2">' + elemRDV[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemCanton[0].outerHTML + '</td><td class="col-md-4">' + elemNote[0].outerHTML + '</td><td class="col-md-2">' + elemDispo[0].outerHTML + '</td></tr>\n\
<tr><td class="col-md-2">' + elemStatut[0].outerHTML + '</td><td class="col-md-4"></td><td class="col-md-2"></td><td class="col-md-2"></td></tr>');

                              elemCoach.append(elemTabBody);
                              $('#divContainer').append(elemCoach);

                              $('[data-toggle="popMatiere"]').popover();
                              $('[data-toggle="overpop"]').popover({html: true});

                              $('[data-toggle="overpop"]').each(function () {
                                  // Compare si le texte n'est pas rouge
                                  if ($(this).css("color") == 'rgb(255, 0, 0)') {
                                      $('#' + $(this)[0].id).popover('disable');
                                  }
                              });
                          });
                      });
                  });


                  // Recupère les heures disponible quand on survole le label et affiche la popover
                  $('#divContainer').on('mouseover', '[data-toggle="overpop"]', function () {
                      var idSpan = $(this)[0].id;
                      var pseudo = $(this)[0].id.toString().split('_')[0];
                      var dateRef = $(this)[0].id.toString().split('_')[1];
                      $('#pseudoText').val(pseudo);
                      var promiseOfTabHours, promiseOfIdPseudo;
                      var objectDate = [];

                      var myDate = new Date(dateRef);

                      var myDateLimitRef = new Date(myDate);
                      myDateLimitRef.setDate(myDate.getDate() + 1);

                      promiseOfIdPseudo = $.get('http://localhost:4242/getIdByPseudo/' + pseudo,
                        function (user) {
                            promiseOfTabHours = $.post('http://localhost:4242/getPlanning', {
                                id_user: user[0]._id,
                                dateLimit: myDateLimitRef.toISOString(),
                                dateNow: myDate.toISOString(),
                                type : 'Coach'
                            },
                              function (dataPop) {
                                  for (var j = 0; j < dataPop.length; j++) {
                                      var tmpDate = new Date(dataPop[j].date);
                                      if (dataPop[j].isFree) {
                                          var momentDate = moment(tmpDate);
                                          objectDate.push(momentDate.format('HH') + ':' + momentDate.format('mm'));
                                      }
                                  }
                                  dataPop.objectDate = objectDate;
                              }
                            );
                        }
                      );

                      promiseOfIdPseudo.then(function () {
                          return promiseOfTabHours;
                      }).then(function (myDataPop) {
                          $('#' + idSpan).attr('data-content', transformObjectToArrayHTML(myDataPop.objectDate));
                          $('#' + idSpan).popover('show');
                      });
                  });

                  // Ferme la popover quand la souris n'est plus au dessus de l'élément
                  $('#divContainer').on('mouseleave', '[data-toggle="overpop"]', function () {
                      var idSpan = $(this)[0].id;
                      $('#' + idSpan).popover('hide');
                  });



                  $('#divContainer').on('click', '[name="rsv"]', function () {
                      var idCoach = $(this)[0].parentNode.parentNode.parentNode.childNodes[2].childNodes[1].childNodes[0].value;
                      var name = $(this)[0].parentNode.parentNode.parentNode.childNodes[2].childNodes[0].childNodes[0];
                      var fullName = name.outerHTML.substr(6, name.outerHTML.length - 13);

                      promiseOfPlanning = $.post('http://localhost:4242/getPlanning', {
                          id_user: idCoach,
                          dateNow: date.toISOString(),
                          type: 'Coach'
                      },
                        function (data) {
                            $.each(data, function (index, d) {
                                var myTitle = (d['isFree'] == true) ? "Libre" : (d['id_student'] == '<?php echo $_SESSION['_id']; ?>') ? '<?php echo $_SESSION['pseudo']; ?>' : 'Occupé';
                                var myStart = new Date(d['date']);
                                var myEnd = transformDateStartToEnd(myStart, d['duration']);
                                var myColor = ((myTitle == "Libre") ? "#1E9C1E" : "#FF0000");
                                var isEditable = (d['isFree'] == true) ? true : false;
                                tabEvents.push({id: d['_id'], title: myTitle, start: myStart, end: myEnd, color: myColor, editable: isEditable});
                            });
                        }
                      );

                      promiseOfPlanning.then(function () {
                          console.log(tabEvents);                          
                          $('#rsvCalendar').fullCalendar('removeEvents');
                          $('#rsvCalendar').fullCalendar('addEventSource', tabEvents);
                          $('#rsvCalendar').fullCalendar('refetchEvents');
                          $('#fullNameText').text(' - ' + fullName);
                          $('#myCalendar').modal('show');                                 
                          $('#rsvCalendar').fullCalendar('today');
                      });
                  });

                  $('#myCalendar').on('hide.bs.modal', function (e) {
                      tabEvents = [];
                      $('#rsvCalendar').fullCalendar('removeEvents');
                  });
                  
                  $('#linkPrevious').click(function(e){
                      e.preventDefault();                      
                      window.location.replace("index.php");
                  });
              });
        </script>
    </body>
</html>

