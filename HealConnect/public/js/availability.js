document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

    // Detect if the user is a clinic (weekly schedule) or independent therapist (specific dates)
    const isClinic = window.userRole === 'clinic'; 

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: window.availabilities.map(avail => {
            if (isClinic) {
                // Weekly recurring event for clinic
                return {
                    title: 'Available',
                    daysOfWeek: [parseInt(avail.day_of_week)], // 0=Sunday
                    startTime: avail.start_time,
                    endTime: avail.end_time,
                    color: avail.is_active ? '#4c8bf5' : '#e0e0e0'
                };
            } else {
                // Specific date for independent therapist
                const dayName = typeof avail.day_of_week === "number"
                    ? dayNames[avail.day_of_week]
                    : avail.day_of_week;

                return {
                    title: `${dayName} (${avail.start_time} - ${avail.end_time})`,
                    start: getNextDateForDay(dayName, avail.start_time),
                    end: getNextDateForDay(dayName, avail.end_time),
                    color: avail.is_active ? '#4c8bf5' : '#e0e0e0'
                };
            }
        })
    });

    calendar.render();
});

// Helper function for independent therapist specific dates
function getNextDateForDay(dayName, timeStr) {
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
    const dayIndex = dayMap[dayName];
    const diff = (dayIndex + 7 - now.getDay()) % 7;

    const nextDate = new Date(now);
    nextDate.setDate(now.getDate() + diff);

    const [hours, minutes] = timeStr.split(':');
    nextDate.setHours(hours, minutes, 0, 0);

    return nextDate.toISOString();
}
