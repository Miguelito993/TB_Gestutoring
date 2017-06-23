jQuery(document).ready(function ($) {
    $('#searchData').click(function () {
        // TODO: Écrire la matière de la session
        $('#matiereText').text(matiere);

        $.getJSON(
            'http://localhost:4242/getIdCoachByPseudo/' + partner,
            function (idCoach) {
                $.getJSON(
                    'http://localhost:4242/getDataList/' + matiere + '/'+idCoach._id,
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

    $('#myDatas').on('hide.bs.modal', function (e) {
        $('.table').html('<tr><th>#</th><th>Type</th><th>Aperçu</th><th>Envoyer</th></tr>').attr('hidden');
        //$('#tablePrivate').html('<tr><th>#</th><th>Type</th><th>Lien</th><th></th></tr>').attr('hidden');
    });
});