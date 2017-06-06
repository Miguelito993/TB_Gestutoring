jQuery(document).ready(function ($) {
    $('#connexForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        $.post(
                'http://localhost:4242/checkLogin',
                {
                    pseudo: $('#inputPseudo').val(),
                    pwd: $('#inputPassword').val()
                            //TODO: Prévoir une méthode pour chiffrer le mot de passe (autre que CryptoJS)
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
                                    //window.location.replace("index.php", 2000);
                                    window.location.reload();
                                    //$('#navMenu').load('./inc/inc_navigation.php');                                                                     
                                }
                        );

                    } else if (data.status == 'Failed') {
                        $("#alertPopUp").attr('class', 'alert alert-danger alert-dismissible');
                        $("#alertPopUp").empty();
                        $("#alertPopUp").append("Login/Password erroné");

                        $("#inputPassword").val("");
                    }

                }
        );        
    });
});



