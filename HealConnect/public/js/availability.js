document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) return; // Safety check

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: window.availabilities.map(avail => ({
            title: `${avail.day_of_week} (${avail.start_time} - ${avail.end_time})`,
            start: getNextDateForDay(avail.day_of_week, avail.start_time),
            end: getNextDateForDay(avail.day_of_week, avail.end_time),
            color: avail.is_active ? '#4c8bf5' : '#e0e0e0'
        })),
    });

    calendar.render();
});

function getNextDateForDay(dayOfWeek, timeStr) {
    const dayMap = {
        Sunday: 0,
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6
    };

    const now = new Date();
    const dayIndex = dayMap[dayOfWeek];
    const diff = (dayIndex + 7 - now.getDay()) % 7;
    const nextDate = new Date(now);
    nextDate.setDate(now.getDate() + diff);

    const [hours, minutes] = timeStr.split(':');
    nextDate.setHours(hours, minutes, 0, 0);

    return nextDate.toISOString();
}
