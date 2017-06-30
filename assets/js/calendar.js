function transformDateStartToEnd(dateStart, duration) {
    var tmp = new Date(dateStart);
    tmp.setUTCHours(tmp.getUTCHours() + duration)
    return tmp;
}

var tabEvents = [];

jQuery(document).ready(function ($) {
    $('#eventForm').submit(function (e) {
        // On désactive le comportement par défaut du navigateur
        e.preventDefault();

        var title = $('#inputTitle').val();
        var date = $('#inputDate').val();
        var time = $('#inputHeure').val().split('h');
        var idCoach = $('#idCoach').val();

        var dateStart = new Date(date);
        dateStart.setHours(time[0], time[1], 0, 0);
        var dateEnd = transformDateStartToEnd(dateStart, 1);

        var eventDate = {title: title,
            start: dateStart,
            end: dateEnd,
            color: "#1E9C1E"
        }
        
        tabEvents.push(eventDate);

        $('#calendar').fullCalendar('renderEvent', eventDate, true);

        $.post('http://localhost:4242/submitMeeting', {
            date: eventDate.start,
            isFree: (eventDate.title == 'Libre') ? true : false,
            duration: 1,
            isEnded: false,
            id_coach: idCoach,
            id_student: null,
            id_matiere: null,
        },
            function (data) {
                $('#myEvent').modal('hide');
            }
        );



        $('#myEvent').on('hide.bs.modal', function (e) {
            $('#inputDate').val('');
            $('#inputHeure').html('');
        });
    });
});