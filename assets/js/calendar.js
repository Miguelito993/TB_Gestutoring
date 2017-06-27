function transformDateStartToEnd(dateStart, duration) {
    var tmp = new Date(dateStart);
    tmp.setUTCHours(tmp.getUTCHours() + duration)
    return tmp;
}

function getPlanningWithIdCoachAndDate(tabCalendar, idCoach, dateNow, isProfil) {
    $.post('http://localhost:4242/getPlanning', {
        id_coach: idCoach,
        dateNow: dateNow
    },
        function (data) {
            $.each(data, function (index, d) {
                $.get(
                    'http://localhost:4242/getPseudoById/' + d['id_student'],
                    function (user) {
                        if (isProfil) {
                            var myTitle = (d['isFree'] == true) ? "Libre" : user[0].pseudo;
                        } else {
                            var myTitle = (d['isFree'] == true) ? "Libre" : "Occupé";
                        }
                        var myStart = new Date(d['date']);
                        var myEnd = transformDateStartToEnd(myStart, d['duration']);
                        var myColor = ((myTitle == "Libre") ? "#1E9C1E" : "#FF0000");
                        tabCalendar.push({title: myTitle, start: myStart, end: myEnd, color: myColor});

                    }
                );
            });
        }
    );

    if (isProfil) {
        $('#calendar').fullCalendar('addEventSource', tabCalendar);
    } else {
        $('#rsvCalendar').fullCalendar('addEventSource', tabCalendar);
    }
    //tabEvents = [];

    console.log(tabCalendar);
    return tabCalendar;
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

        $('#calendar').fullCalendar('renderEvent', eventDate, true); // stick? = true

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