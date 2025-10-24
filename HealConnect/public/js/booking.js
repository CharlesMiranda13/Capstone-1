document.addEventListener('DOMContentLoaded', function () {
    const dateSelect = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const dataElement = document.getElementById('availabilities-data');

    if (!dataElement) {
        console.warn("No availabilities-data found");
        return;
    }

    const availabilities = JSON.parse(dataElement.textContent);

    function formatTime24to12(time24) {
        const [hourStr, minuteStr] = time24.split(':');
        let hour = parseInt(hourStr);
        const suffix = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12; // converts 0 → 12
        return `${hour}:${minuteStr} ${suffix}`;
    }

    dateSelect.addEventListener('change', function () {
        const selectedDate = this.value;
        timeSelect.innerHTML = '<option value="">-- Select Date First --</option>';

        const match = availabilities.find(a => a.date === selectedDate);
        if (match) {
            const startHour = parseInt(match.start_time.split(':')[0]);
            const endHour = parseInt(match.end_time.split(':')[0]);

            timeSelect.innerHTML = '<option value="">-- Choose Time --</option>';

            for (let hour = startHour; hour < endHour; hour++) {
                const timeStr = `${String(hour).padStart(2, '0')}:00:00`; 
                const option = document.createElement('option');
                option.value = timeStr;
                option.textContent = formatTime24to12(timeStr); 
                timeSelect.appendChild(option);
            }
        }
    });
});
