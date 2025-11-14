document.addEventListener('DOMContentLoaded', function () {
    const dateSelect = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const dataElement = document.getElementById('availabilities-data');
    const bookedDataElement = document.getElementById('booked-times-data');

    if (!dataElement) {
        console.warn("No availabilities-data found");
        return;
    }

    const availabilities = JSON.parse(dataElement.textContent);
    const bookedTimes = bookedDataElement ? JSON.parse(bookedDataElement.textContent) : {};

    function formatTime24to12(time24) {
        const [hourStr, minuteStr] = time24.split(':');
        let hour = parseInt(hourStr);
        const suffix = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;
        return `${hour}:${minuteStr} ${suffix}`;
    }

    dateSelect.addEventListener('change', function () {
        const selectedDate = this.value;
        timeSelect.innerHTML = '<option value="">-- Select Date First --</option>';

        const match = availabilities.find(a => a.date === selectedDate);
        if (!match) return;

        const taken = bookedTimes[selectedDate] || [];

        timeSelect.innerHTML = '<option value="">-- Choose Time --</option>';

        const startHour = parseInt(match.start_time.split(':')[0]);
        const endHour = parseInt(match.end_time.split(':')[0]);

        for (let hour = startHour; hour < endHour; hour++) {
            const timeStr = `${String(hour).padStart(2, '0')}:00:00`;

            // Skip booked times
            if (taken.includes(timeStr)) continue;

            const option = document.createElement('option');
            option.value = timeStr;
            option.textContent = formatTime24to12(timeStr);
            timeSelect.appendChild(option);
        }
    });
});
