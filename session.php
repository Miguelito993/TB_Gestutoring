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
        <link rel="icon" href="./assets/img/logo_GesTutoring.ico">
        <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="./bootstrap/css/bootstrap-theme.css">
        <link rel="stylesheet" href="./bootstrap/css/theme.css">
        <link rel="stylesheet" href="./bootstrap/css/signin.css">
        <link rel="stylesheet" href="./assets/css/session.css">
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

        <div class="container">            
            <!-- Get local audio/video stream -->
            <div id="step1">
                <p>Cliquez sur `Autoriser` en haut de l'écran afin que nous puissions accéder à votre webcam et votre microphone pour les appels.</p>
                <div id="step1-error">
                    <p>Impossible d'accèder à la webcam et au microphone. </p>
                    <a href="#" id="step1-retry">Rafraîchir</a>
                </div>
            </div>
            <!-- Make calls to others -->
            <div id="step2" hidden>
                <h2 class="text-center">Merci d'attendre votre correspondant</h2> 
            </div>
                        
            <div id="infoUtil" class="fixed-top"></div>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td colspan="2" class="col-xs-12"><h2 id="titleSession" hidden></h2></td>
                    </tr>
                    <tr>                
                        <td class="col-xs-8"><div id="chatbox" class="connection form-control" hidden></div></td>
                        <td class="col-xs-4">
                            <div id="video-container-1" hidden>
                                <video id="their-video" height="300" class="embed-responsive-item div-video-peer" crossorigin="anonymous" autoplay></video>                    
                            </div>
                        </td>                
                    </tr>                
                    <tr>
                        <td class="col-xs-8">                                
                            <form id="send" class="row" hidden>
                                <div class="form-group">
                                    <div class="col-xs-10">
                                        <input type="text" id="text" class="form-control" placeholder="Votre message">
                                    </div>
                                    <input class="btn btn-success" type="submit" value="Envoyer">
                                </div>
                            </form>                            
                        </td>
                        <td rowspan="2" class="col-xs-4">
                            <div id="video-container-2" hidden>
                                <video id="my-video" height="150" class="embed-responsive-item div-video-me" crossorigin="anonymous" muted="true" autoplay></video>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-xs-8">
                            <?php
                                if ($_SESSION['type'] == 'Coach') {
                                    echo '<button type="button" id="searchData" class="btn btn-info btn-block" data-toggle="modal" data-target="#myDatas" hidden>Recherche de contenu</button>';
                                }
                            ?>
                        </td>
                    </tr>  
                    <tr>
                        <td class="col-xs-8"></td>
                        <td class="col-xs-4">
                            <div id="step3" hidden>  
                                <span id="my-id" hidden></span>
                                <span id="their-id" hidden></span>
                                <p><button type="button" id="end-call" class="btn btn-danger form-control">Terminer l'appel</button></p>
                            </div>
                        </td>
                    </tr>
                    
                </table>
            </div>
        </div> <!-- /container -->    

        <!-- Modal -->
        <?php
        include './inc/modal_datas.php';
        include './inc/modal_notation.php';
        ?>
        <!-- /Modal -->

        <!-- Include external JS libs. -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="http://cdn.peerjs.com/0.3/peer.min.js"></script>  
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>

        <!-- Include internal JS libs. -->
        <script src="./bootstrap/js/bootstrap.js"></script>
        <script src="./assets/js/spin.min.js"></script>
        <script src="./assets/js/session.js"></script>
        <script>
            // Compatibility shim
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

            var opts = {
                lines: 13 // The number of lines to draw
                , length: 28 // The length of each line
                , width: 14 // The line thickness
                , radius: 42 // The radius of the inner circle
                , scale: 1 // Scales overall size of the spinner
                , corners: 1 // Corner roundness (0..1)
                , color: '#000' // #rgb or #rrggbb or array of colors
                , opacity: 0.25 // Opacity of the lines
                , rotate: 0 // The rotation offset
                , direction: 1 // 1: clockwise, -1: counterclockwise
                , speed: 1 // Rounds per second
                , trail: 60 // Afterglow percentage
                , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
                , zIndex: 2e9 // The z-index (defaults to 2000000000)
                , className: 'spinner' // The CSS class to assign to the spinner
                , top: '50%' // Top position relative to parent
                , left: '50%' // Left position relative to parent
                , shadow: false // Whether to render a shadow
                , hwaccel: false // Whether to use hardware acceleration
                , position: 'absolute' // Element positioning
            }

            // No API key required when not using cloud server
            var peer = new Peer('<?php echo $_SESSION['_id']; ?>', {host: 'localhost', port: 4242, path: '/peerjs'});
            var socket = io.connect('http://localhost:4242');
            var c;
            var partner;
            var partnerID;
            var matiere;
            var idSession;

            var promiseOfMatiere;
            var promiseOfPartner;
            var promiseOfMeeting = $.post('http://localhost:4242/getMeeting', {
                type: '<?php echo $_SESSION['type']; ?>',
                myID: '<?php echo $_SESSION['_id']; ?>'
            },
                function (data) {
                    console.log(data);
                    var meetingFound = false;
                    $.each(data, function (index, d) {
                        var myDate = moment(d['date']);
                        partnerID = ('<?php echo $_SESSION['type']; ?>' == 'Coach') ? d['id_student'] : d['id_coach'];
                        var myIDMatiere = d['id_matiere'];

                        // Réservation ok
                        //TODO: Ne pas oublier de remodifier les valeurs du temps pour une période +- 5 minutes
                        if (myDate.isBetween(moment().subtract(45, 'minutes'), moment().add(45, 'minutes'))) {
                            promiseOfMatiere = $.get(
                                'http://localhost:4242/getMatiereByID/' + myIDMatiere
                                );
                            promiseOfPartner = $.get(
                                'http://localhost:4242/getPseudoById/' + partnerID
                                );
                            data.idSession = d['_id'];
                            meetingFound = true;
                            return false; // Break jQuery each loop
                        }
                    });
                    if (!meetingFound) {
                        alert('Vous n\'avez pas de rendez-vous prochainement');
                        window.location.replace('index.php');                        
                    }
                }
            );            

            var infoExtra = null;
            var spinner = new Spinner(opts).spin();

            peer.on('open', function () {
                $('#my-id').text(peer.id);
            });

            // Wait connections from others
            peer.on('connection', connect);

            peer.on('error', function (err) {
                console.log(err);
            });

            peer.on('disconnected', function () {
                if (!peer.destroyed) {
                    cleanVars();
                }
            });

            function connect(c) {
                if (c.label === 'info') {
                    c.on('data', function (data) {
                        infoExtra = data;
                    });
                } else if (c.label === 'chat') {
                    var chatbox = $('#chatbox');
                    var messages = $('<div><em>Correspondant connecté</em></div>').addClass('messages');
                    chatbox.append(messages);

                    $('#chatbox').prepend(chatbox);

                    c.on('data', function (data) {
                        messages.append('<div><span class="peer">' + partner + '</span>: ' + data + '</div>');
                    });
                } else if (c.label === 'file') {
                    c.on('data', function (data) {                        
                        
                        // If we're getting a file, create a URL for it.
                        if (data.constructor === ArrayBuffer) {
                            var dataView = new Uint8Array(data);

                            var typeText = infoExtra.type.split('/');
                            var url;
                            if (typeText[0] === 'image' || typeText[0] === 'text' || typeText[1] === 'pdf') {
                                var dataFile = new File([dataView], infoExtra.name, {type: infoExtra.type, lastModified: infoExtra.lastModified});
                                url = window.URL.createObjectURL(dataFile);
                            } else {
                                var dataBlob = new Blob([dataView]);
                                url = window.URL.createObjectURL(dataBlob);
                            }
                            $('#chatbox').find('.messages').append('<div><span class="file">' +
                                partner + ' vous a envoyé un <a target="_blank" href="' + url + '">fichier</a>.</span></div>');
                        }else{  
                            // Fichier reçu de la banque de données
                            $('#chatbox').find('.messages').append('<div><span class="file">' +
                                partner + ' vous a envoyé un <a target="_blank" href="' + data + '">fichier</a>.</span></div>');
                        }
                        infoExtra = null;
                    });
                }
            }

            // Receiving a call
            peer.on('call', function (call) {
                // Answer the call automatically (instead of prompting user) for demo purposes
                call.answer(window.localStream);
                step3(call);
            });
            peer.on('error', function (err) {
                alert(err.message);
                // Return to step 2 if error occurs
                cleanVars();
            });

            function step1() {
                // Get audio/video stream
                navigator.getUserMedia({audio: true, video: true}, function (stream) {
                    // Set your video displays
                    $('#my-video').prop('src', URL.createObjectURL(stream));

                    window.localStream = stream;
                    step2();
                }, function () {
                    $('#step1-error').show();
                    alert("Branchez votre webcam avant de lancer une session");
                    window.location.replace('index.php');
                });
            }

            function step2() {
                $('#step1, #step3, #video-container-1, #video-container-2, #chatbox, #searchData, #send').hide();
                $('#step2').prepend(spinner.el);
                $('#step2').show();
            }

            function step3(call) {
                // Hang up on an existing call if present
                if (window.existingCall) {
                    window.existingCall.close();
                }

                // Wait for stream on the call, then set peer video display
                call.on('stream', function (stream) {
                    $('#their-video').prop('src', URL.createObjectURL(stream));
                });

                // UI stuff
                window.existingCall = call;
                $('#their-id').text(call.peer);
                call.on('close', cleanVars);
                $('#step1, #step2').hide();
                spinner.stop();
                $('#step3, #video-container-1, #video-container-2, #chatbox, #searchData, #titleSession, #send').show();
                $('#titleSession').text("Session de chat avec " + partner);
                $('#infoUtil').append('<h4>' + matiere + '</h4>');
                // TODO: Ajouter un chronomètre 

            }

            function cleanVars() {
                step2();
                if('<?php echo $_SESSION['type']; ?>' == 'Student'){
                    $('#myNotation').modal('show');
                    $('#idUser').val(partnerID);
                }else{
                    socket.emit('close_session', {idSession: idSession});
                    socket.emit('close_socket', {myPseudo: '<?php echo $_SESSION['pseudo']; ?>', partnerPseudo: partner});                    
                }
                socket.close(true);
                partner = null;
                partnerID = null;
                infoExtra = null;
                matiere = null;
                idSession = null;
                c = null;
                $('#infoUtil').html('');
                peer.destroy();

                console.log("Destroy peer");
                console.log(peer);
                //window.location.replace(index.php);
                
                //TODO: Prévoir d'afficher la durée de la session et l'argent dépensé/gagné
            }
                
            /* 
            //Alternative des lignes de codes suivantes      
                promiseOfMeeting.done(function (data) {
                idSession = data.idSession;

                promiseOfMatiere.done(function (mat) {
                    matiere = mat[0].name;
                    promiseOfPartner.done(function (user) {
                        partner = user[0].pseudo;

                        if ('<?php echo $_SESSION['type']; ?>' == 'Coach') {
                            socket.emit('nouveau_client', {pseudo: '<?php echo $_SESSION['pseudo']; ?>', myID: '<?php echo $_SESSION['_id']; ?>', type: '<?php echo $_SESSION['type']; ?>', tarif: '<?php echo $_SESSION['tarif']; ?>', myPartner: partner});
                        } else {
                            socket.emit('nouveau_client', {pseudo: '<?php echo $_SESSION['pseudo']; ?>', myID: '<?php echo $_SESSION['_id']; ?>', type: '<?php echo $_SESSION['type']; ?>', myPartner: partner});
                        }                        
                    });
                });
            });
            */
               
            promiseOfMeeting.then(function (data) {                
                idSession = data.idSession;                
                return promiseOfMatiere;
            })
            .then(function (mat){
                matiere = mat[0].name;
                return promiseOfPartner;
            })
            .then(function (user){
                partner = user[0].pseudo;
                if ('<?php echo $_SESSION['type']; ?>' == 'Coach') {
                    socket.emit('nouveau_client', {pseudo: '<?php echo $_SESSION['pseudo']; ?>', myID: '<?php echo $_SESSION['_id']; ?>', type: '<?php echo $_SESSION['type']; ?>', tarif: '<?php echo $_SESSION['tarif']; ?>', myPartner: partner});
                } else {
                    socket.emit('nouveau_client', {pseudo: '<?php echo $_SESSION['pseudo']; ?>', myID: '<?php echo $_SESSION['_id']; ?>', type: '<?php echo $_SESSION['type']; ?>', myPartner: partner});
                }  
            });



            socket.on('find_partner', function (info) {
                var requestedPeer = info.partnerID;
                partner = info.partnerName;

                // Initiate a call!
                var call = peer.call(requestedPeer, window.localStream);

                // Create 2 connections, one labelled chat and another labelled file
                c = peer.connect(requestedPeer, {
                    label: 'chat',
                    serialization: 'none',
                    metadata: {message: 'I want to chat with you!'}
                });
                c.on('open', function () {
                    connect(c);
                });
                c.on('error', function (err) {
                    alert(err);
                });

                var f = peer.connect(requestedPeer, {
                    label: 'file',
                    reliable: true
                });
                f.on('open', function () {
                    connect(f);
                });
                f.on('error', function (err) {
                    alert(err);
                });

                var i = peer.connect(requestedPeer, {
                    label: 'info',
                    serialization: 'json'
                });
                i.on('open', function () {
                    connect(i);
                });
                i.on('error', function (err) {
                    alert(err);
                });

                step3(call);
            });

            $(document).ready(function () {
                // Prepare file drop box.
                var boxDrop = $('#chatbox');
                boxDrop.on('dragenter', function () {
                    $(this).css('border', '3px dashed red');
                    return false;
                });
                boxDrop.on('dragover', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).css('border', '3px dashed red');
                    return false;
                });
                boxDrop.on('dragleave', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).css('border', '');
                    return false;
                });

                boxDrop.on('drop', function (e) {
                    e.originalEvent.preventDefault();
                    var file = e.originalEvent.dataTransfer.files[0];

                    var infoFile = {type: file.type, name: file.name, lastModified: file.lastModified};

                    $(this).css('border', '');
                    var theirId = $('#their-id').text();
                    var conns = peer.connections[theirId];

                    if (conns[2].label === 'file') {
                        conns[3].send(infoFile);
                        conns[2].send(file);
                        $(this).find('.messages').append('<div><span class="file">Vous avez envoyé un fichier</span></div>');
                    }
                });

                $('#send').submit(function (e) {
                    e.preventDefault();

                    var msg = $('#text').val();
                    var theirId = $('#their-id').text();
                    var conns = peer.connections[theirId];
                    
                    console.log(msg);

                    if (conns[1].label === 'chat') {
                        conns[1].send(msg);
                        $('.connection').find('.messages').append('<div><span class="you">Vous: </span>' + msg
                            + '</div>');
                    }

                    $('#text').val('');
                    $('#text').focus();
                });

                $('#end-call').click(function () {
                    window.existingCall.close();
                    peer.destroy();                    
                });

                // Retry if getUserMedia fails
                $('#step1-retry').click(function () {
                    $('#step1-error').hide();
                    step1();
                });

                // Get things started
                step1();
            });

        </script>            
    </body>
</html>

