document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

    // Detect if the user is a clinic (weekly schedule) or independent therapist (specific dates)
    const isClinic = window.userRole === 'clinic';
    
    console.log('Calendar initialized');
    console.log('User role:', window.userRole);
    console.log('Availabilities:', window.availabilities); 

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        timeZone: 'local', 
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
                // Specific date event for independent therapist    
                const dateStr = avail.date;
                const startTimeStr = avail.start_time; 
                const endTimeStr = avail.end_time;
                
                // Combine date and time in ISO format to prevent timezone shifts
                const startDateTime = dateStr + 'T' + startTimeStr;
                const endDateTime = dateStr + 'T' + endTimeStr;

                const dayName = typeof avail.day_of_week === "number"
                    ? dayNames[avail.day_of_week]
                    : avail.day_of_week;

                return {
                    title: `${dayName} (${formatTime(avail.start_time)} - ${formatTime(avail.end_time)})`,
                    start: startDateTime,
                    end: endDateTime,
                    color: avail.is_active ? '#4c8bf5' : '#e0e0e0',
                    extendedProps: {
                        isActive: avail.is_active
                    }
                };
            }
        })
    });

    calendar.render();
});

// Helper function to format time for display
function formatTime(timeStr) {
    const [hours, minutes] = timeStr.split(':');
    const hour = parseInt(hours);
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${period}`;
}