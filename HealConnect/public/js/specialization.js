document.addEventListener("DOMContentLoaded", function () {

    // Add new specialization field
    const addBtn = document.getElementById('add-specialization');
    const wrapper = document.getElementById('specialization-wrapper');

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            const item = document.createElement('div');
            item.classList.add('specialization-item');
            item.style.display = 'flex';
            item.style.gap = '10px';
            item.style.marginBottom = '8px';

            item.innerHTML = `
                <input type="text" name="specialization[]" class="specialization-input">
                <button type="button" class="remove-spec"
                    style="background:#dc3545;color:white;border:none;padding:6px 10px;border-radius:6px;">X</button>
            `;

            wrapper.appendChild(item);

            // Attach remove event to new buttons
            addRemoveEvent(item.querySelector('.remove-spec'));
        });
    }

    // Remove specialization field
    function addRemoveEvent(button) {
        button.addEventListener('click', function () {
            this.parentElement.remove();
        });
    }

    // Attach remove events to existing buttons
    document.querySelectorAll('.remove-spec').forEach(btn => {
        addRemoveEvent(btn);
    });

});
