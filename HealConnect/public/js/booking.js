document.addEventListener('DOMContentLoaded', function () {
    const dateSelect = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const dataElement = document.getElementById('availabilities-data');
    const bookedDataElement = document.getElementById('booked-times-data');

    if (!dataElement) return console.warn("No availabilities-data found");

    const availabilities = JSON.parse(dataElement.textContent);
    const bookedTimes = bookedDataElement ? JSON.parse(bookedDataElement.textContent) : {};

    function formatTime24to12(time24) {
        const [hourStr, minuteStr] = time24.split(':');
        let hour = parseInt(hourStr);
        const suffix = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;
        return `${hour}:${minuteStr} ${suffix}`;
    }

    // Populate date select for clinic (day_of_week) or independent (date)
    function populateDates() {
        const today = new Date();
        dateSelect.innerHTML = '<option value="">-- Choose Date --</option>';

        availabilities.forEach(slot => {
            if (slot.date) {
                // Independent therapist: use the date directly
                const formatted = new Date(slot.date).toISOString().split('T')[0];
                const optionText = new Date(slot.date).toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' });
                const option = document.createElement('option');
                option.value = formatted;
                option.textContent = optionText;
                dateSelect.appendChild(option);
            } else if (slot.day_of_week !== undefined) {
                // Clinic: generate next 4 weeks for this day
                for (let i = 0; i < 28; i++) {
                    const checkDate = new Date();
                    checkDate.setDate(today.getDate() + i);
                    if (checkDate.getDay() === slot.day_of_week) {
                        const formatted = checkDate.toISOString().split('T')[0];
                        const optionText = checkDate.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' });
                        const option = document.createElement('option');
                        option.value = formatted;
                        option.textContent = optionText;
                        dateSelect.appendChild(option);
                    }
                }
            }
        });
    }

    populateDates();

    dateSelect.addEventListener('change', function () {
        const selectedDate = this.value;
        timeSelect.innerHTML = '<option value="">-- Choose Time --</option>';

        const dateObj = new Date(selectedDate);
        const dayOfWeek = dateObj.getDay();

        // Get all slots for this date
        const slotsForDate = availabilities.filter(slot => {
            if (slot.date) return slot.date === selectedDate; // Independent
            if (slot.day_of_week !== undefined) return slot.day_of_week === dayOfWeek; // Clinic
            return false;
        });

        if (!slotsForDate.length) return;

        const taken = bookedTimes[selectedDate] || [];

        slotsForDate.forEach(slot => {
            let start = slot.start_time.split(':').map(Number);
            let end = slot.end_time.split(':').map(Number);

            let currentHour = start[0];
            let currentMinute = start[1] || 0;

            while (currentHour < end[0] || (currentHour === end[0] && currentMinute < (end[1] || 0))) {
                const timeStr = `${String(currentHour).padStart(2, '0')}:${String(currentMinute).padStart(2, '0')}`;

                if (!taken.includes(timeStr + ':00')) {
                    const option = document.createElement('option');
                    option.value = timeStr + ':00';
                    option.textContent = formatTime24to12(timeStr);
                    timeSelect.appendChild(option);
                }

                // increment by 60 minutes
                currentMinute += 60;
                if (currentMinute >= 60) {
                    currentMinute = 0;
                    currentHour++;
                }
            }
        });
    });
});
