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
            <h2 id="titleSession">PeerJS video chat</h2>
                        
            <div id="step4" class="div-chat"> 
                <div id="chatbox" class="connection form-control"></div>
                <form id="send">
                    <input type="text" id="text" placeholder="Votre message">
                    <input class="btn" type="submit" value="Envoyer">
                </form>
            </div>
            
            <!-- Video area -->
            <div id="video-container" class="div-video">
              <video id="their-video" width="450" height="280" class="embed-responsive-item peer-video" crossorigin="anonymous" autoplay></video>
              <video id="my-video" width="250" height="150" class="embed-responsive-item my-video" crossorigin="anonymous" muted="true" autoplay></video>
            </div>
            
            
            <!-- Steps -->
            <div>
              
              <!-- Get local audio/video stream -->
              <div id="step1">
                <p>Please click `allow` on the top of the screen so we can access your webcam and microphone for calls.</p>
                <div id="step1-error">
                  <p>Failed to access the webcam and microphone. Make sure to run this demo on an http server and click allow when asked for permission by the browser.</p>
                  <a href="#" id="step1-retry">Try again</a>
                </div>
              </div>

              <!-- Make calls to others -->
              <div id="step2">
                <p>Your id: <span id="my-id">...</span></p>
                <p>Share this id with others so they can call you.</p>
                <h3>Make a call</h3>
                <div class="pure-form">
                  <input type="text" placeholder="Call user id..." id="callto-id">
                  <a href="#" id="make-call">Call</a>
                </div>
              </div>

              <!-- Call in progress -->
              <div id="step3">
                <p>Currently in call with <span id="their-id">...</span></p>
                <p><button type="button" id="end-call" class="btn btn-danger">Terminer l'appel</button></p>
              </div>
            </div>

        </div> <!-- /container -->    
       
        <!-- Modal -->
        <?php
            include './inc/modal_inscrip.php';
        ?>
        <!-- /Modal -->
           
        <!-- Include external JS libs. -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="http://cdn.peerjs.com/0.3/peer.min.js"></script>  
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>
        
        <!-- Include internal JS libs. -->
        <script src="./bootstrap/js/bootstrap.js"></script> 
        <script src="./assets/js/connexion.js"></script>
        <script src="./assets/js/inscription.js"></script>
        <script>
            
            // Compatibility shim
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
            
            // No API key required when not using cloud server
            var peer = new Peer('<?php echo $_SESSION['_id']; ?>',{host: 'localhost', port: 4242, path: '/peerjs'});
            var socket = io.connect('http://localhost:4242');    
            var c;
            var partner;
            if('<?php echo $_SESSION['pseudo']; ?>' == 'Alexterrieur'){
                partner = 'IronMan';
            }else if('<?php echo $_SESSION['pseudo']; ?>' == 'IronMan'){
                partner = 'Alexterrieur';
            }else if('<?php echo $_SESSION['pseudo']; ?>' == 'Marvel'){
                partner = 'Alainterrieur';
            }else{
                partner = 'Marvel';
            }
            //var partner = ('<?php echo $_SESSION['pseudo']; ?>' == 'Alexterrieur')?'IronMan':'Alexterrieur';
            var infoExtra = null;
            
            peer.on('open', function(){                
                $('#my-id').text(peer.id);
            });
            
            // Wait connections from others
            peer.on('connection', connect);
            
            peer.on('error', function(err){
                console.log(err);
            });
            
            peer.on('disconnected', function(){
                if(!peer.destroyed){
                    cleanVars(); 
                }
            });
                             
            function connect(c){
                if(c.label === 'info'){
                    c.on('data', function(data){
                        infoExtra = data;
                    }); 
                }else if(c.label === 'chat'){
                    var chatbox = $('#chatbox');
                    var messages = $('<div><em>Peer connected</em></div>').addClass('messages');
                    chatbox.append(messages);
                    
                    $('#step4').prepend(chatbox);
                    
                    c.on('data', function(data){
                        messages.append('<div><span class="peer">' + partner + '</span>: ' + data + '</div>');
                    });
                }else if (c.label === 'file') {
                    c.on('data', function(data) {  
                      console.log(infoExtra.type);
                                   
                      // If we're getting a file, create a URL for it.
                      if (data.constructor === ArrayBuffer) {
                        var dataView = new Uint8Array(data);                        
                        
                        var typeText = infoExtra.type.split('/'); 
                        var url;
                        if(typeText[0] === 'image' || typeText[0] === 'text' || typeText[1] === 'pdf'){
                            var dataFile = new File([dataView], infoExtra.name, {type: infoExtra.type, lastModified: infoExtra.lastModified});
                            url = window.URL.createObjectURL(dataFile);
                        }else{
                            var dataBlob = new Blob([dataView]);
                            url = window.URL.createObjectURL(dataBlob);
                        }
                        $('#chatbox').find('.messages').append('<div><span class="file">' +
                            partner + ' has sent you a <a target="_blank" href="' + url + '">file</a>.</span></div>');
                      }
                      infoExtra = null;
                    });
                }
            }

            // Receiving a call
            peer.on('call', function(call){
              // Answer the call automatically (instead of prompting user) for demo purposes
              call.answer(window.localStream);
              step3(call);
            });
            peer.on('error', function(err){
              alert(err.message);
              // Return to step 2 if error occurs
              cleanVars();
            });

            function step1 () {
              // Get audio/video stream
              navigator.getUserMedia({audio: true, video: true}, function(stream){
                // Set your video displays
                $('#my-video').prop('src', URL.createObjectURL(stream));

                window.localStream = stream;
                step2();
              }, function(){ $('#step1-error').show(); });
            }

            function step2 () {
              $('#step1, #step3, #step4, #video-container, #chatbox').hide();
              $('#step2').show();
            }

            function step3 (call) {                
              // Hang up on an existing call if present
              if (window.existingCall) {
                window.existingCall.close();
              }

              // Wait for stream on the call, then set peer video display
              call.on('stream', function(stream){
                $('#their-video').prop('src', URL.createObjectURL(stream));
              });

              // UI stuff
              window.existingCall = call;
              $('#their-id').text(call.peer);
              call.on('close', cleanVars);              
              $('#step1, #step2').hide();
              $('#step3, #step4, #video-container, #chatbox').show();
              $('#titleSession').text("Session de chat avec "+partner);
              
            }
            
            function cleanVars(){
                step2();
                socket.emit('close_socket',{myPseudo: '<?php echo $_SESSION['pseudo']; ?>', partnerPseudo: partner});
                socket.close(true);
                partner = null;
                infoExtra = null;
                c = null; 
                peer.destroy();
                
                console.log("Destroy peer");
                console.log(peer);
                //window.location.replace(index.php);
            }
            
            socket.emit('nouveau_client', {pseudo: '<?php echo $_SESSION['pseudo']; ?>', myID: '<?php echo $_SESSION['_id']; ?>', myPartner: partner});          
                 
            socket.on('find_partner', function(info){
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
                c.on('open', function(){
                    connect(c);
                });
                c.on('error', function(err){ alert(err);});
                
                var f = peer.connect(requestedPeer, {
                    label: 'file',
                    reliable: true                    
                });
                f.on('open', function(){
                    connect(f);
                });
                f.on('error', function(err){ alert(err);});                
                
                var i = peer.connect(requestedPeer, {
                    label: 'info',
                    serialization: 'json'                    
                });
                i.on('open', function(){
                    connect(i);
                });
                i.on('error', function(err){ alert(err);});

                step3(call);
            });
            
            $(document).ready(function() {
                // Prepare file drop box.
                var boxDrop = $('#chatbox');
                boxDrop.on('dragenter', function(){
                    $(this).css('border', '3px dashed red');                    
                    return false;
                });
                boxDrop.on('dragover', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).css('border', '3px dashed red');                    
                    return false;
                });
                boxDrop.on('dragleave', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).css('border', '');                    
                    return false;
                });
               
                boxDrop.on('drop', function(e){
                    e.originalEvent.preventDefault();
                    var file = e.originalEvent.dataTransfer.files[0];
                   
                    var infoFile = {type: file.type, name: file.name, lastModified: file.lastModified};
                                        
                    $(this).css('border', ''); 
                    var theirId = $('#their-id').text();
                    var conns = peer.connections[theirId];                             
                                                            
                    if (conns[2].label === 'file') {
                      conns[3].send(infoFile);
                      conns[2].send(file);
                      $(this).find('.messages').append('<div><span class="file">You sent a file.</span></div>');
                    }                 
                });
                                
                $('#send').submit(function(e) {
                    e.preventDefault();

                    var msg = $('#text').val();
                    var theirId = $('#their-id').text();
                    var conns = peer.connections[theirId];                   

                      if (conns[1].label === 'chat') {
                        conns[1].send(msg);
                        $('.connection').find('.messages').append('<div><span class="you">You: </span>' + msg
                          + '</div>');
                      }

                    $('#text').val('');
                    $('#text').focus();
                });

              $('#end-call').click(function(){
                window.existingCall.close();
                peer.destroy();
                step2();
              });

              // Retry if getUserMedia fails
              $('#step1-retry').click(function(){
                $('#step1-error').hide();
                step1();
              });
              
              // Get things started
              step1();
            });
                        
        </script>            
    </body>
</html>

