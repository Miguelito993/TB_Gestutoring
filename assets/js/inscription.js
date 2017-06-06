function StudentAccount() {
    $('#divEmailParent').show();
    $('#divEmailParent input').attr('required', 'true');

    //----------------------------------------------------
    $('#divTarif').hide();
    $('#divTarif input').removeAttr('required');
    $('#divTarif input').val("");

    $('#divMatiere').hide();
    $('#divMatiere select').removeAttr('required');
    $('#divMatiere select').val("");

    $('#divDiplome').hide();
    $('#divDiplome input').removeAttr('required');
    $('#divDiplome input').val("");
    //----------------------------------------------------              
}

function CoachAccount() {
    $('#divTarif').show();
    $('#divTarif input').attr('required', 'true');

    $('#divMatiere').show();
    $('#divMatiere select').attr('required', 'true');

    $('#divDiplome').show();
    $('#divDiplome input').attr('required', 'true');

    //----------------------------------------------------
    $('#divEmailParent').hide();
    $('#divEmailParent input').removeAttr('required');
    $('#divEmailParent input').val("");
    //----------------------------------------------------                
}

$('input[name=inputType]').change(function () {
    var typeValue = $('input[name=inputType]:checked').val();

    if (typeValue === "Student") {
        StudentAccount();
    } else {
        CoachAccount();
    }
});

jQuery(document).ready(function ($) {

    $('[data-toggle="popover"]').popover();

    // Rempli le formulaire des cantons
    $.getJSON(
            'http://localhost:4242/getDepartments',
            function (data) {
                $.each(data, function (index, d) {
                    $('#inputCity').append("<option>" + d['name'] + "</option>");
                });
            }
    );

    // Rempli le formulaire des matières
    $.getJSON(
            'http://localhost:4242/getMatieres',
            function (data) {
                $.each(data, function (index, d) {
                    $('#inputMatiere').append("<option>" + d['name'] + "</option>");
                });
            }
    );

    $('#inscripForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        var form = $('#inscripForm')[0];
        var data = new FormData(form);

        //TODO: Vérifier que les deux passwords sont identiques.
        //      Vérifier que le pseudo et le mail ne sont pas déjà présents dans la base de données.


        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: 'http://localhost:4242/submitInscription',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                console.log("Success: ", data);
                $('#alertPopUp').attr('class', 'alert alert-success');
                $('#alertPopUp').attr('role', 'alert');
                $('#alertPopUp').empty();
                $('#alertPopUp').append("Inscription validé");

                setTimeout(function () {
                    $('#myInscription').modal('hide')
                }, 2000);
            },
            error: function (e) {
                console.log("Error: ", e);
            }
        });

        $('#myInscription').on('hidden.bs.modal', function () {
            $(this).find("input,select")
                    .val('')
                    .end();                    

            $('#submitInscription').attr('value', 'Inscription');
            $('#alertPopUp').removeAttr('class');
            $('#alertPopUp').empty();
        });
    });
});