/**
 * Global UI & Modal System
 */

// --- CORE ENGINE ---
const HealModal = {
    modal: null,
    titleEl: null,
    messageEl: null,
    confirmBtn: null,
    cancelBtn: null,
    closeBtn: null,
    resolveCallback: null,

    init() {
        this.modal = document.getElementById('customModal');
        if (!this.modal) return;

        this.titleEl = document.getElementById('modalTitle');
        this.messageEl = document.getElementById('modalMessage');
        this.confirmBtn = document.getElementById('modalConfirm');
        this.cancelBtn = document.getElementById('modalCancel');
        this.closeBtn = this.modal.querySelector('.modal-close');

        if (this.confirmBtn) this.confirmBtn.addEventListener('click', () => this.handleAction(true));
        if (this.cancelBtn) this.cancelBtn.addEventListener('click', () => this.handleAction(false));
        if (this.closeBtn) this.closeBtn.addEventListener('click', () => this.handleAction(false));

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.handleAction(false);
        });

        document.addEventListener('keydown', (e) => {
            if (!this.modal.classList.contains('active')) return;
            if (e.key === 'Escape') this.handleAction(false);
            if (e.key === 'Enter') {
                e.preventDefault();
                this.handleAction(true);
            }
        });
    },

    confirm(options = {}) {
        return new Promise((resolve) => {
            if (!this.modal) {
                console.warn('HealModal: Modal element not found in DOM.');
                resolve(confirm(options.message || 'Proceed?'));
                return;
            }
            this.resolveCallback = resolve;
            this.titleEl.textContent = options.title || 'Confirm Action';
            this.messageEl.textContent = options.message || 'Are you sure you want to proceed?';
            this.confirmBtn.textContent = options.confirmText || 'Confirm';
            this.cancelBtn.textContent = options.cancelText || 'Cancel';
            this.confirmBtn.className = 'modal-btn ' + (options.type === 'danger' ? 'modal-btn-danger' : 'modal-btn-primary');
            
            this.modal.classList.add('active');
            this.modal.setAttribute('aria-hidden', 'false');
            this.confirmBtn.focus();
        });
    },

    handleAction(result) {
        this.modal.classList.remove('active');
        this.modal.setAttribute('aria-hidden', 'true');
        if (this.resolveCallback) {
            this.resolveCallback(result);
            this.resolveCallback = null;
        }
    }
};

window.confirmAction = async function(element, message, title = 'Confirm') {
    const confirmed = await HealModal.confirm({ title, message, type: 'danger' });
    if (confirmed) {
        if (element.tagName === 'A') window.location.href = element.href;
        else if (element.tagName === 'BUTTON' && element.form) element.form.submit();
        else if (typeof element === 'function') element();
    }
};

// --- UTILITIES ---
function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alerts-container .alert, .alert-success, .alert-danger');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
}

function setupSimpleModal(modalId, openBtnSelector, closeBtnSelector, displayType = "flex") {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    document.querySelectorAll(openBtnSelector).forEach(btn => {
        btn.addEventListener("click", (e) => { e.preventDefault(); modal.style.display = displayType; });
    });
    const closeBtn = modal.querySelector(closeBtnSelector);
    closeBtn?.addEventListener("click", () => { modal.style.display = "none"; });
    window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });
    if (modal.querySelector(".success-msg")?.textContent.trim() !== "") modal.style.display = displayType;
}

function setupDynamicModal(modalId, bodyId, btnSelector, urlBuilder) {
    const modal = document.getElementById(modalId);
    const body = document.getElementById(bodyId);
    if (!modal || !body) return;
    document.querySelectorAll(btnSelector).forEach(btn => {
        btn.addEventListener("click", function () {
            fetch(urlBuilder(this), { headers: { "X-Requested-With": "XMLHttpRequest" } })
                .then(res => res.text())
                .then(html => { body.innerHTML = html; modal.style.display = "flex"; })
                .catch(err => console.error("Error loading modal content:", err));
        });
    });
    modal.querySelector(".close")?.addEventListener("click", () => { modal.style.display = "none"; body.innerHTML = ""; });
    window.addEventListener("click", (e) => { if (e.target === modal) { modal.style.display = "none"; body.innerHTML = ""; } });
}

// --- FEATURE HANDLERS ---
function setupDeleteHandler(selector, urlBuilder) {
    document.querySelectorAll(selector).forEach(button => {
        button.addEventListener("click", async function () {
            const row = this.closest("tr");
            const confirmed = await HealModal.confirm({
                title: 'Confirm Delete',
                message: 'Are you sure you want to delete this record? This action cannot be undone.',
                type: 'danger',
                confirmText: 'Delete'
            });
            if (!confirmed) return;
            fetch(urlBuilder(this.getAttribute("data-id")), {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => { if (data.success) row?.remove(); })
            .catch(err => console.error("Error deleting record:", err));
        });
    });
}

function setupTabSwitchWithDetection() {
    const tabs = document.querySelectorAll(".tab-link");
    const confirmBtn = document.getElementById("confirmTabSwitch");
    const modal = document.getElementById("tabSwitchModal");
    const forms = document.querySelectorAll(".tab-content form, #settingsForm");
    if (!tabs.length || !modal || !forms.length) return;

    let pendingTab = null;
    let formChanged = false;
    let originalData = {};

    const capture = () => {
        originalData = {};
        forms.forEach(f => f.querySelectorAll('input, textarea, select').forEach(i => { if (i.name) originalData[i.name] = i.value; }));
    };
    const hasChanged = () => {
        let changed = false;
        forms.forEach(f => f.querySelectorAll('input, textarea, select').forEach(i => {
            if (i.name && originalData[i.name] !== i.value) changed = true;
        }));
        return changed;
    };

    capture();
    forms.forEach(f => f.addEventListener('input', () => formChanged = hasChanged()));
    tabs.forEach(t => t.addEventListener('click', (e) => {
        e.preventDefault();
        if (t.classList.contains('active')) return;
        pendingTab = t.dataset.tab;
        if (formChanged) modal.style.display = "flex";
        else switchTab(pendingTab);
    }));

    confirmBtn?.addEventListener("click", () => {
        switchTab(pendingTab);
        modal.style.display = "none";
        setTimeout(() => { capture(); formChanged = false; }, 100);
    });

    const switchTab = (name) => {
        document.querySelectorAll(".tab-link").forEach(t => t.classList.remove("active"));
        document.querySelector(`.tab-link[data-tab="${name}"]`)?.classList.add("active");
        document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
        document.getElementById(name)?.classList.add("active");
    };
}

function setupPasswordUpdateConfirmation() {
    const form = document.querySelector("form");
    const saveBtn = document.querySelector(".save-btn");
    const modal = document.getElementById("passwordConfirmModal");
    const confirmBtn = document.getElementById("confirmPasswordUpdate");

    if (!form || !saveBtn || !modal || !confirmBtn) return;

    let bypass = false;
    saveBtn.addEventListener("click", function (e) {
        if (bypass) return;
        const current = document.getElementById("current_password")?.value;
        const newPass = document.getElementById("new_password")?.value;
        const confirm = document.getElementById("confirm_password")?.value;

        if (current || newPass || confirm) {
            e.preventDefault();
            e.stopPropagation();
            modal.style.display = "flex";
        }
    });

    confirmBtn.addEventListener("click", function () {
        modal.style.display = "none";
        bypass = true;
        form.requestSubmit ? form.requestSubmit(saveBtn) : form.submit();
    });
}

// --- INITIALIZATION ---
document.addEventListener("DOMContentLoaded", function () {
    HealModal.init();
    autoDismissAlerts();

    // Standard Modals
    setupSimpleModal("addEmployeeModal", "#addEmployeeBtn", ".close");
    setupSimpleModal("forgotModal", ".openForgotBtn", ".close", "flex");
    setupSimpleModal("tabSwitchModal", ".openTabSwitchModal", ".closeTabSwitch", "flex");
    setupSimpleModal("passwordConfirmModal", ".openPasswordModal", ".closePasswordModal", "flex");

    // Dynamic Content Modals
    setupDynamicModal("patientModal", "modal-body", ".openModalBtn", (btn) => btn.getAttribute("data-link"));
    setupDynamicModal("employeeModal", "employeeModalBody", ".schedule-btn, .edit-btn", (btn) => {
        const id = btn.getAttribute("data-id");
        const action = btn.classList.contains("schedule-btn") ? "schedule" : "edit";
        return `/clinic/employees/${id}/${action}`;
    });

    // Delete Handlers
    setupDeleteHandler(".delete-btn", (id) => `/clinic/employees/${id}`);

    // Tab Detection
    setupTabSwitchWithDetection();

    // Password Update Confirmation
    setupPasswordUpdateConfirmation();

    // Event Delegation
    document.addEventListener('click', async (e) => {
        const target = e.target.closest('[data-delete-availability], [data-delete-schedule], [data-cancel-appointment], .btn-trigger-delete, #cancelSubscriptionBtn');
        if (!target) return;

        let options = { type: 'danger', confirmText: 'Confirm', cancelText: 'Cancel' };

        if (target.hasAttribute('data-delete-availability')) {
            options.title = 'Delete Availability';
            options.message = 'Are you sure you want to delete this availability slot?';
        } else if (target.hasAttribute('data-delete-schedule')) {
            options.title = 'Delete Schedule';
            options.message = 'Are you sure you want to delete this schedule?';
        } else if (target.hasAttribute('data-cancel-appointment')) {
            options.title = 'Cancel Appointment';
            options.message = 'Are you sure you want to cancel this appointment?';
            options.confirmText = 'Yes, Cancel';
        } else if (target.classList.contains('btn-trigger-delete')) {
            options.title = 'Permanently Delete User?';
            options.message = `You are about to delete "${target.dataset.name}". This cannot be undone.`;
            options.confirmText = 'Yes, Delete Permanently';
        } else if (target.id === 'cancelSubscriptionBtn') {
            options.title = 'Cancel Subscription';
            options.message = 'Are you sure you want to cancel this subscription?';
            options.confirmText = 'Yes, Cancel Subscription';
        }

        const confirmed = await HealModal.confirm(options);
        if (confirmed) {
            if (target.classList.contains('btn-trigger-delete')) {
                const form = document.getElementById('deleteUserForm');
                form.action = target.dataset.action;
                form.submit();
            } else if (target.id === 'cancelSubscriptionBtn') {
                document.getElementById('cancelSubscriptionForm').submit();
            } else {
                target.closest('form')?.submit();
            }
        }
    });
});
