/*
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fichier JavaScript utile pour les sessions de communication
*/
var note = null;

jQuery(document).ready(function ($) {
    $('#searchData').click(function () {        
        $('#matiereText').text(matiere);

        $.getJSON(
            'http://localhost:4242/getIdByPseudo/' + myPseudo,
            function (idCoach) {
                $.getJSON(
                    'http://localhost:4242/getDataList/' + matiere + '/'+idCoach[0]._id,
                    function (data) {   
                        var indexPublic = 1;
                        var indexPrivate = 1;
                        $.each(data, function (index, d) {
                            if(d['access'] == 'public'){
                                $('#dataPublic').removeAttr('hidden');
                                $('#tablePublic').append('<tr><td>'+indexPublic++ +'</td><td>'+d['typeData']+'</td><td>'+d['keywords']+'</td><td><input id="'+d['data']+'" type="button" class="btn btn-success" name="dataBtn" value="Envoyer"></td></tr>');
                            }else{
                                $('#dataPrivate').removeAttr('hidden');
                                $('#tablePrivate').append('<tr><td>'+indexPrivate++ +'</td><td>'+d['typeData']+'</td><td>'+d['keywords']+'</td><td><input id="'+d['data']+'" type="button" class="btn btn-success" name="dataBtn" value="Envoyer"></td></tr>');
                            }
                        });                        
                    }
                );
            }
        );
    });
    
    // Envoi le fichier de la banque de données au partenaire
    $('.modal-body').on('click', '[name="dataBtn"]', function(){                
        var infoFile = {type: 'application/pdf', name: $(this)[0].id, lastModified: moment()};        
        
        var theirId = $('#their-id').text();
        var conns = peer.connections[theirId];
                
        if (conns[2].label === 'file') {
            conns[3].send(infoFile);
            conns[2].send('http://localhost:4242/getFile/datas/' + $(this)[0].id);
            $('#chatbox').find('.messages').append('<div><span class="file">Vous avez envoyé un fichier</span></div>');            
        }
        $('#myDatas').modal('hide');
    });
    
    $('#notationForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        if(note != null){
        var comment = $('#inputComment').val();        
        var idUser = $('#idUser').val();        
        
        $.post('http://localhost:4242/submitNotation', {
            note: note,
            comment: comment,
            id_coach: idUser,            
        },
            function (data) {                
                $('#myNotation').modal('hide');
                window.location.replace('index.php');
            }
        );

        }else{
            return alert("Veuillez choisir une note");
        }
    });
    
    $(".starLink").click(function(e) {
        e.preventDefault();

        var href = $(this).attr('href');
        note = href.substr(1,href.length);
        
        var elements = document.getElementsByClassName("starLink");
        
        for(var i=0;i<elements.length;i++){
            if(note >= elements.length - i){
                elements[i].style.color = 'orange';                
            }else{
                elements[i].style.color = '#aaa';                
            }
        }
    });

    $('#myDatas').on('hide.bs.modal', function (e) {
        $('#tablePrivate, #tablePublic').html('<tr><th>#</th><th>Type</th><th>Mot-clés</th><th></th></tr>').attr('hidden');
        //$('#tablePrivate').html('<tr><th>#</th><th>Type</th><th>Lien</th><th></th></tr>').attr('hidden');
    });
    
    $('#myNotation').on('hide.bs.modal', function (e) {
            $('#inputNote').val('');
            $('#inputComment').val('');
            note = null;
        });
});