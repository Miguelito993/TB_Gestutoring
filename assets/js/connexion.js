/*
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fichier JavaScript pour la connexion
*/
jQuery(document).ready(function ($) {
    $('#connexForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        $.post(
          'http://localhost:4242/checkLogin',
          {
              pseudo: $('#inputLogPseudo').val(),
              pwd: sha1($('#inputLogPassword').val())
          }
        ,
          function (data) {
              if (data.status == 'Success') {
                  $("#alertPopUp").attr('class', 'alert alert-success alert-dismissible');
                  $("#alertPopUp").empty();
                  $("#alertPopUp").append("Connexion réussie");

                  $.post('fillSession.php', {myUser: data.docs[0]});
                  $.post('http://localhost:4242/changeStatus', {
                      id: data.docs[0]._id,
                      statusOnline: true
                  },
                    function (data) {
                        // Changé la page
                        setTimeout(function () {
                            window.location.replace("index.php");
                        }, 2000);
                    }
                  );

              } else if (data.status == 'Failed') {
                  $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');
                  $("#alertPopUp").empty();
                  $("#alertPopUp").append("Combinaison Pseudo/Mot de passe erroné");

                  $("#inputLogPassword").val("");
              } else if (data.status == 'NotValid') {
                  $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');
                  $("#alertPopUp").empty();
                  $("#alertPopUp").append("Votre compte n'a pas encore été validé");

                  $("#inputLogPassword").val("");
              }
          }
        );
    });
});