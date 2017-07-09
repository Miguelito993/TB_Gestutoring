jQuery(document).ready(function ($) {
    $('#connexForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        $.post(
                'http://localhost:4242/checkLogin',
                {
                    pseudo: $('#inputLogPseudo').val(),
                    pwd: $('#inputLogPassword').val()
                    //TODO: Chiffrer le mot de passe avec sha1 (exemple dans le fichier inscription.js)
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
                                    console.log(data);
                                    //$('.navbar-right').hide();                  
                                    // Changé la page
                                    setTimeout(function(){window.location.replace("index.php");},2000);
                                    //$('#navMenu').load('./inc/inc_navigation.php');                                                                     
                                }
                        );

                    } else if (data.status == 'Failed') {
                        $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');
                        $("#alertPopUp").empty();
                        $("#alertPopUp").append("Combinaison Pseudo/Mot de passe erroné");

                        $("#inputLogPassword").val("");
                    }

                }
        );        
    });
});



