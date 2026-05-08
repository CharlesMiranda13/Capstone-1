document.addEventListener("DOMContentLoaded", function () {
    const specInput = document.getElementById('new-spec-input');
    const addBtn = document.getElementById('add-spec-btn');
    const container = document.getElementById('spec-tag-container');

    if (!specInput || !addBtn || !container) return;

    function createTag(text) {
        text = text.trim();
        if (!text) return;

        // Check if duplicate
        const existing = Array.from(container.querySelectorAll('input[type="hidden"]'))
            .map(input => input.value.toLowerCase());
        
        if (existing.includes(text.toLowerCase())) {
            specInput.classList.add('is-invalid');
            setTimeout(() => specInput.classList.remove('is-invalid'), 2000);
            return;
        }

        const tag = document.createElement('div');
        tag.className = 'spec-tag';
        tag.innerHTML = `
            <span>${text}</span>
            <input type="hidden" name="specialization[]" value="${text}">
            <i class="fa fa-times remove-tag"></i>
        `;

        container.appendChild(tag);
        specInput.value = '';
    }

    // Add on button click
    addBtn.addEventListener('click', () => createTag(specInput.value));

    // Add on Enter key
    specInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            createTag(specInput.value);
        }
    });

    // Remove tags (Event Delegation)
    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-tag')) {
            e.target.closest('.spec-tag').remove();
        }
    });
});
