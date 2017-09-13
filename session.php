<!--
Travail de Bachelor 2017 - GesTutoring
Auteur: Miguel Pereira Vieira
Date: 12.07.2017
Lieu: Genève
Version: 1.0

Page de session vidéo et chat
-->
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
            
            <div id="step1">
                <p>Cliquez sur `Autoriser` en haut de l'écran afin que nous puissions accéder à votre webcam et votre microphone pour les appels.</p>
                <div id="step1-error">
                    <p>Impossible d'accèder à la webcam et au microphone. </p>
                    <a href="#" id="step1-retry">Rafraîchir</a>
                </div>
            </div>
            
            <div id="step2" hidden>
                <h2 class="text-center">Merci d'attendre votre correspondant</h2> 
            </div>
                        
            <div id="infoUtil" class="fixed-top"></div>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td colspan="2" class="col-xs-12"><h2 id="titleSession" style="text-align: center;" hidden></h2></td>
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
        <script src="./assets/js/sha1.js"></script>
        <script>
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
            
            var peer = new Peer('<?php echo $_SESSION['_id']; ?>', {host: 'localhost', port: 4242, path: '/peerjs'});
            var socket = io.connect('http://localhost:4242');
            var c;
            var partner;
            var partnerID;
            var matiere;
            var idSession;
            var myPseudo = '<?php echo $_SESSION['pseudo']; ?>';

            var promiseOfMatiere;
            var promiseOfPartner;
            var promiseOfMeeting = $.post('http://localhost:4242/getMeeting', {
                type: '<?php echo $_SESSION['type']; ?>',
                myID: '<?php echo $_SESSION['_id']; ?>'
            },
                function (data) {                    
                    var meetingFound = false;
                    $.each(data, function (index, d) {
                        var myDate = moment(d['date']);
                        partnerID = ('<?php echo $_SESSION['type']; ?>' == 'Coach') ? d['id_student'] : d['id_coach'];
                        var myIDMatiere = d['id_matiere'];

                        if (myDate.isBetween(moment().subtract(5, 'minutes'), moment().add(5, 'minutes'))) {
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

            // Attente de connexion
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

            // Réception de l'appel
            peer.on('call', function (call) {
                // Répond à l'appel automatiquement
                call.answer(window.localStream);
                step3(call);
            });
            peer.on('error', function (err) {
                alert(err.message);
                cleanVars();
            });

            function step1() {
                // Recupère le stream audio/vidéo
                navigator.getUserMedia({audio: true, video: true}, function (stream) {
                    // Assigne le streaming à la balise viédo
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
                // Raccroche un appel s'il y en existe un
                if (window.existingCall) {
                    window.existingCall.close();
                }

                // Attente de recevoir un stream, ensuite l'assigne à la balise video
                call.on('stream', function (stream) {
                    $('#their-video').prop('src', URL.createObjectURL(stream));
                });
                
                window.existingCall = call;                
                $('#their-id').text(call.peer);
                call.on('close', cleanVars);
                $('#step1, #step2').hide();
                spinner.stop();
                $('#step3, #video-container-1, #video-container-2, #chatbox, #searchData, #titleSession, #send').show();
                $('#titleSession').text("Session de chat avec " + partner);
                $('#infoUtil').append('<h4>' + matiere + '</h4>');
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
                if('<?php echo $_SESSION['type']; ?>' == 'Coach'){
                    window.location.replace('index.php');
                }
            }              
               
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

                // Appel le partenaire
                var call = peer.call(requestedPeer, window.localStream);

                // Créer 3 connexion, une pour le chat, une pour les fichiers et la dernière pour les compléments de fichiers
                c = peer.connect(requestedPeer, {
                    label: 'chat',
                    serialization: 'none'                    
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
                // Code de drag and drop pour les fichiers
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

                // Recharge la zone de stream
                $('#step1-retry').click(function () {
                    $('#step1-error').hide();
                    step1();
                });

                step1();
            });

        </script>            
    </body>
</html>