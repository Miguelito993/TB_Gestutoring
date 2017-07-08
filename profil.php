<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header('Location: index.php');
    exit();
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
            <div id="divInfoProfil" class="table-responsive">
                <table class="table">  
                    <tr>
                        <td colspan="3">Infos personnelle</td>    
                    </tr>
                    <tr>
                        <td class="col-xs-2">Prénom</td>
                        <td class="col-xs-6"><input type="text" class="form-control" id="p_inputFirstname" name="p_inputFirstname" value="<?php echo $_SESSION['prenom']; ?>"></td>
                        <td class="col-xs-4" rowspan="4"><img id="imgProfil" class="img-responsive"  alt="Image de profil" height="200" width="200"></td>
                    </tr>
                    <tr>
                        <td>Nom</td>
                        <td><input type="text" class="form-control" id="p_inputName" name="p_inputName" value="<?php echo $_SESSION['nom']; ?>"></td>
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        <td><input type="email" class="form-control" id="p_inputEmail" name="p_inputEmail" value="<?php echo $_SESSION['email']; ?>"></td>
                    </tr>
                    <tr>
                        <td>Nom d'utilisateur</td>
                        <td><span id="p_inputPseudo" name="p_inputPseudo"><?php echo $_SESSION['pseudo']; ?></span></td>
                    </tr>
                    <tr>
                        <td>Mot de passe</td>
                        <td><input type="password" class="form-control" id="p_inputPassword" name="p_inputPassword" value="<?php echo $_SESSION['password']; ?>"></td>
                        <td>Cell 13</td>
                    </tr>
                    <tr>
                        <td>Canton</td>
                        <td><select class="form-control" id="p_inputCity" name="p_inputCity">
                                <!-- Formulaire rempli à l'aide de jQuery -->
                            </select></td>
                        <td>Cell 16</td>
                    </tr>
                    <tr>
                        <td>Soldes</td>
                        <td><span id="p_inputSoldes" name="p_inputSoldes"><?php echo number_format($_SESSION['soldes'], 2); ?> CHF</span></td>
                        <td>Cell 19</td>
                    </tr>
                    <tr>
                        <td>Type de compte</td>
                        <td><span id="p_inputType" name="p_inputType"><?php echo ($_SESSION['type'] == 'Coach') ? 'Répétiteur' : 'Étudiant'; ?></span></td>
                        <td>Cell 21</td>
                    </tr>
                    <?php
                    if ($_SESSION['type'] == 'Coach') {
                        $tarif = $_SESSION['tarif'];
                        $diplomes = "";
                        foreach ($_SESSION['diplomes'] as $value) {
                            $diplomes .= '<a href="http://localhost:4242/getFile/uploads/' . $value . '" target="_blank">' . $value . '</a><br/>';
                        }
                        echo "<tr>
                                <td>Tarif</td>
                                <td><input type=\"number\" class=\"form-control\" id=\"p_inputTarif\" name=\"p_inputTarif\" step=\"1\" min=\"1\" max=\"50\" value=\"$_SESSION[tarif]\"></td>
                                <td>Cell 24</td>
                              </tr>
                              <tr>
                                <td>Diplômes</td>
                                <td id=\"p_cellDiplomes\">$diplomes</td>
                                <td>Cell 27</td>
                              </tr>
                              <tr>
                                <td>Matières</td>
                                <td><select multiple class=\"form-control\" id=\"p_inputMatiere\" name=\"inputMatiere\">
                                    
                                </select></td>
                                <td>Cell 30</td>
                              </tr>";
                    } else if ($_SESSION['type'] == 'Student') {
                        echo "<tr>
                                <td>Email d'un parent</td>
                                <td><input type=\"email\" class=\"form-control\" id=\"p_inputEmailParent\" name=\"p_inputEmailParent\" value=\"$_SESSION[emailParent]\"></td>
                                <td>Cell 24</td>
                              </tr>";
                    }
                    ?>
                    <tr>
                        <td>Cell 25</td>
                        <td>Cell 26</td>
                        <td><button type="button" id="btnSave" class="btn btn-primary btn-block" disabled="disabled">Sauvegarder</button></td>
                    </tr>
                </table>
            </div>
          

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

              var promiseOfGetPseudo;
              var promiseOfProfil = $.post('http://localhost:4242/getPlanning', {
                  id_coach: myID,
                  dateNow: date.toISOString()
              },
                function (data) {
                    $.each(data, function (index, d) {
                        promiseOfGetPseudo = $.get(
                          'http://localhost:4242/getPseudoById/' + d['id_student'],
                          function (user) {
                              var myTitle = (d['isFree'] == true) ? "Libre" : user[0].pseudo;
                              var myStart = new Date(d['date']);
                              var myEnd = transformDateStartToEnd(myStart, d['duration']);
                              var myColor = ((myTitle == "Libre") ? "#1E9C1E" : "#FF0000");
                              tabEvents.push({id: d['_id'], title: myTitle, start: myStart, end: myEnd, color: myColor});
                          }
                        );
                    });
                }
              );

              jQuery(document).ready(function ($) {
                  if ('<?php echo $_SESSION['img_profil']; ?>' != '') {
                      $('#imgProfil').attr('src', 'http://localhost:4242/getFile/img/<?php echo $_SESSION['img_profil']; ?>');
                  }

                  $('#p_inputFirstname, #p_inputName, #p_inputEmail, #p_inputPassword, #p_inputCity, #p_inputEmailParent, #p_inputTarif, #p_inputMatiere').change(function(){                     
                    $(this).css('border-color', 'blue');
                    $('#btnSave').removeAttr('disabled');
                  });
              
                  $('#btnSave').click(function(e){
                      // On désactive le comportement par défaut du navigateur
                      e.preventDefault();
                      
                      //TODO: Créer la route sur le serveur pour la modification de données
                      console.log($('#p_inputMatiere').val());
                  });

                                    
                  // Rempli le formulaire des cantons
                  $.getJSON(
                    'http://localhost:4242/getDepartments',
                    function (data) {
                        $.each(data, function (index, d) {
                            if (d['name'] == '<?php echo $_SESSION['canton']; ?>') {
                                $('#p_inputCity').append("<option selected>" + d['name'] + "</option>");
                            } else {
                                $('#p_inputCity').append("<option>" + d['name'] + "</option>");
                            }

                        });
                    }
                  );

                  // Rempli le formulaire des matières
                  $.getJSON(
                    'http://localhost:4242/getMatieres',
                    function (data) {
                        var tabMatiere = <?php echo json_encode($_SESSION['matieres']); ?>;                        

                        $.each(data, function (index, d) {
                            if ($.inArray(d['name'], tabMatiere) != -1) {
                                $('#p_inputMatiere').append("<option selected>" + d['name'] + "</option>");
                            } else {
                                $('#p_inputMatiere').append("<option>" + d['name'] + "</option>");
                            }
                        });
                    }
                  );


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
                      },
                      dayRender: function (date, cell) {
                          if (moment().diff(date, 'days') > 0) {
                              cell.css("background-color", "silver");
                          }
                      }

                  });

                  promiseOfProfil.then(function () {
                      return promiseOfGetPseudo;
                  }).then(function(){                      
                      console.log(tabEvents);
                      $('#calendar').fullCalendar('removeEvents');
                      $('#calendar').fullCalendar('addEventSource', tabEvents);
                      $('#calendar').fullCalendar('refetchEvents');
                  });

              });
        </script>
    </body>
</html>

