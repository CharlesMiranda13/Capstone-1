document.addEventListener('DOMContentLoaded', () => {
    const availabilitiesTag = document.getElementById('availabilities-data');
    const availabilities = JSON.parse(availabilitiesTag.textContent);

    const dateSelect = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');

    dateSelect.addEventListener('change', function () {
        const selectedDate = this.value;
        timeSelect.innerHTML = '';

        // Find all slots for selected date
        const slots = availabilities.filter(a => a.date === selectedDate);

        if (slots.length > 0) {
            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = `${slot.start_time}-${slot.end_time}`;
                option.textContent = `${slot.start_time} - ${slot.end_time}`; // you can format if needed
                timeSelect.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.textContent = '-- No available time --';
            timeSelect.appendChild(option);
        }
    });
});
