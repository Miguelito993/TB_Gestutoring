var note = null;

jQuery(document).ready(function ($) {
    $('#searchData').click(function () {        
        $('#matiereText').text(matiere);

        $.getJSON(
            'http://localhost:4242/getIdByPseudo/' + partner,
            function (idCoach) {
                $.getJSON(
                    'http://localhost:4242/getDataList/' + matiere + '/'+idCoach[0]._id,
                    function (data) {
                        //TODO: Envoyer directement le pdf avec une prévisualisation dans la liste du contenu disponible
                        
                        var indexPublic = 1;
                        var indexPrivate = 1;
                        $.each(data, function (index, d) {
                            if(d['access'] == 'public'){
                                $('#dataPublic').removeAttr('hidden');
                                $('#tablePublic').append('<tr><td>'+indexPublic++ +'</td><td>'+d['typeData']+'</td><td>'+d['data']+'</td><td>Un lien</td></tr>');
                            }else{
                                $('#dataPrivate').removeAttr('hidden');
                                $('#tablePrivate').append('<tr><td>'+indexPrivate++ +'</td><td>'+d['typeData']+'</td><td>'+d['data']+'</td><td>Un lien</td></tr>');
                            }
                        });                        
                    }
                );
            }
        );
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
                console.log(data)
                $('#myNotation').modal('hide');
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
        $('#tablePrivate, #tablePublic').html('<tr><th>#</th><th>Type</th><th>Aperçu</th><th>Envoyer</th></tr>').attr('hidden');
        //$('#tablePrivate').html('<tr><th>#</th><th>Type</th><th>Lien</th><th></th></tr>').attr('hidden');
    });
    
    $('#myNotation').on('hide.bs.modal', function (e) {
            $('#inputNote').val('');
            $('#inputComment').val('');
            note = null;
        });
});