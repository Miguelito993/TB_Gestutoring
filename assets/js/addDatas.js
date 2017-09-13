/*
    Travail de Bachelor 2017 - GesTutoring
    Auteur: Miguel Pereira Vieira
    Date: 12.07.2017
    Lieu: Genève
    Version: 1.0

    Fichier JavaScript utile pour ajouter du contenu
*/
jQuery(document).ready(function ($) {
    $('[data-toggle="popoverKey"]').popover({html: true});    
    $('#linkAddDatas').click(function (e) {
        e.preventDefault();

        // Rempli le formulaire des matières
        $.getJSON(
            'http://localhost:4242/getMatieres',
            function (data) {
                $.each(data, function (index, d) {
                    $('#ad_inputMatiere').append("<option value=\""+d['_id']+"\">" + d['name'] + "</option>");
                });
                $('#myAddDatas').modal('show');
            }
        );
    });
    
    $('#addDatasForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();             
        
        var form = $('#addDatasForm')[0];        
        var data = new FormData(form);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: 'http://localhost:4242/submitDatas',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                console.log("Success: ", data);
                $('#alertPopUpAddDatas').attr('class', 'alert alert-success');
                $('#alertPopUpAddDatas').attr('role', 'alert');
                $('#alertPopUpAddDatas').empty();
                $('#alertPopUpAddDatas').append("Support ajouté");

                setTimeout(function () {
                    $('#myAddDatas').modal('hide');
                    window.location.replace('index.php');
                }, 2000);
            },
            error: function (e) {
                console.log("Error: ", e);
            }
        });

        $('#myAddDatas').on('hide.bs.modal', function () {
            $(this).find("input,textarea")
                    .val('')
                    .end();                    

            $('#submitAddDatas').attr('value', 'Ajouter contenu');
            $('#alertPopUpAddDatas').removeAttr('class');
            $('#alertPopUpAddDatas').empty();
        });
    });
});