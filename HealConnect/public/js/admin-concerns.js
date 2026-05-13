console.log("admin-concerns.js: Initialization started");

document.addEventListener('DOMContentLoaded', function() {
    console.log("admin-concerns.js: DOMContentLoaded fired");
    const modal = document.getElementById('concernViewModal');
    const modalBody = document.getElementById('concernModalBody');
    const closeBtn = modal?.querySelector('.close');

    if (!modal || !modalBody) {
        console.error("admin-concerns.js: Modal elements NOT found!", {modal, modalBody});
        return;
    }

    console.log("admin-concerns.js: Modal ready, attaching click listener");

    // Use event delegation on the document for robustness
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.openConcernModal');
        if (!item) return;

        console.log("admin-concerns.js: Concern item clicked", item.dataset.id);
        e.preventDefault();
        if (item.classList.contains('loading')) return;

        const url = item.getAttribute('data-link');
        console.log("admin-concerns.js: Fetching from", url);
        
        item.classList.add('loading');
        modalBody.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i> Loading concern details...</div>';
        modal.style.display = 'flex';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error("HTTP error " + res.status);
            return res.text();
        })
        .then(html => {
            console.log("admin-concerns.js: Fetch success");
            modalBody.innerHTML = html;
            item.querySelector('.badge')?.remove();
        })
        .catch(err => {
            modalBody.innerHTML = '<p style="color:red; text-align:center;">Error loading concern. Please try again.</p>';
            console.error("admin-concerns.js: Fetch Error:", err);
        })
        .finally(() => {
            item.classList.remove('loading');
        });
    });

    closeBtn?.addEventListener('click', () => {
        modal.style.display = 'none';
        modalBody.innerHTML = '';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            modalBody.innerHTML = '';
        }
    });
});
