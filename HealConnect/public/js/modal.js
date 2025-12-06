document.addEventListener("DOMContentLoaded", function () {
  function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alerts-container .alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
        }, 500);
      }, 3000); // Dismiss after 3 seconds
    });
  }
  autoDismissAlerts();
  // ---------- GENERIC MODAL SETUP ----------
  function setupModal(modalId, openBtnSelector, closeBtnSelector, displayType = "flex") {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const openBtns = document.querySelectorAll(openBtnSelector);
    const closeBtn = modal.querySelector(closeBtnSelector);

    openBtns.forEach(btn => {
      btn.addEventListener("click", (e) => {
         e.preventDefault();
        modal.style.display = displayType;
      });
    });

    closeBtn?.addEventListener("click", () => {
      modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });

    const hasSuccess = modal.querySelector(".success-msg");
      if (hasSuccess && hasSuccess.textContent.trim() !== "") {
          modal.style.display = displayType; 
        }
  }

  // ---------- DYNAMIC MODAL SETUP ----------
  function setupDynamicModal(modalId, bodyId, btnSelector, urlBuilder) {
    const modal = document.getElementById(modalId);
    const body = document.getElementById(bodyId);
    if (!modal || !body) return;

    const closeBtn = modal.querySelector(".close");

    document.querySelectorAll(btnSelector).forEach(btn => {
      btn.addEventListener("click", function () {
        const url = urlBuilder(this);
        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then(res => res.text())
          .then(html => {
            body.innerHTML = html;
            modal.style.display = "flex";
          })
          .catch(err => console.error("Error loading modal content:", err));
      });
    });

    closeBtn?.addEventListener("click", () => {
      modal.style.display = "none";
      body.innerHTML = "";
    });

    window.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
        body.innerHTML = "";
      }
    });
  }

  // ---------- DELETE HANDLER ----------
  function setupDeleteHandler(selector, urlBuilder) {
    document.querySelectorAll(selector).forEach(button => {
      button.addEventListener("click", function () {
        const id = this.getAttribute("data-id");

        if (confirm("Are you sure you want to delete this employee?")) {
          fetch(urlBuilder(id), {
            method: "DELETE",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              "X-Requested-With": "XMLHttpRequest"
            }
          })
            .then(res => res.json())
            .then(data => {
              if (data.success) this.closest("tr").remove();
              else alert("Failed to delete employee.");
            })
            .catch(err => console.error("Error deleting employee:", err));
        }
      });
    });
  }

  // ---------- IMAGE MODAL IN CHAT ----------
  function setupImageModal() {
    const imageModal = document.getElementById("imageModal");
    const modalImage = document.getElementById("modalImage");
    const closeModal = imageModal?.querySelector(".close");
    const chatMessages = document.getElementById("chat-messages");

    if (!imageModal || !chatMessages) return;

    chatMessages.addEventListener("click", function (e) {
      if (e.target.classList.contains("chat-file") && e.target.tagName === "IMG") {
        modalImage.src = e.target.src;
        imageModal.style.display = "block";
      }
    });

    closeModal?.addEventListener("click", () => {
      imageModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === imageModal) imageModal.style.display = "none";
    });
  }

  // ---------- VIEW IMAGE MODAL (VALID ID / LICENSE) ----------
  function setupImageView(triggerId, modalId, imageId, closeId, dataAttr, displayType = "flex") {
    const trigger = document.getElementById(triggerId);
    const modal = document.getElementById(modalId);
    const img = document.getElementById(imageId);
    const closeBtn = document.getElementById(closeId);
    if (!trigger || !modal || !img || !closeBtn) return;

    const imagePath = trigger.getAttribute(dataAttr);

    trigger.addEventListener("click", (e) => {
      e.preventDefault();
      img.src = imagePath;
      modal.style.display = displayType;
    });

    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });
  }

  // ================= TAB SWITCH WITH CHANGE DETECTION  =================
  function setupTabSwitchModal() {
    const tabs = document.querySelectorAll(".tab-link");
    const confirmBtn = document.getElementById("confirmTabSwitch");
    const tabSwitchModal = document.getElementById("tabSwitchModal");

    if (!tabs.length || !confirmBtn || !tabSwitchModal) return;

    // Check if it's admin settings or shared settings 
    const settingsForm = document.getElementById("settingsForm");
    const formsInsideTabs = document.querySelectorAll(".tab-content form");
    
    // Determine which forms to track
    let formsToTrack = [];
    if (settingsForm) {
      // Admin settings: track the single form
      formsToTrack = [settingsForm];
    } else if (formsInsideTabs.length > 0) {
      // Shared settings: track all forms inside tabs
      formsToTrack = Array.from(formsInsideTabs);
    }
    
    if (formsToTrack.length === 0) return;

    let pendingTab = null;
    let formChanged = false;
    let originalFormData = {};

    // Capture original form values on page load
    function captureFormData() {
      originalFormData = {};
      
      formsToTrack.forEach(form => {
        form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], textarea, select').forEach(field => {
          if (field.name && !field.disabled) {
            originalFormData[field.name] = field.value || '';
          }
        });
      });
    }

    // Check if any form has changed
    function hasFormChanged() {
      let changed = false;
      
      formsToTrack.forEach(form => {
        const currentFields = form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], textarea, select');
        
        for (let field of currentFields) {
          if (field.name && !field.disabled) {
            const originalValue = originalFormData[field.name] || '';
            const currentValue = field.value || '';
            
            if (originalValue !== currentValue) {
              changed = true;
              break;
            }
          }
        }

        // Check file inputs
        const fileInputs = form.querySelectorAll('input[type="file"]');
        for (let field of fileInputs) {
          if (field.name && field.files.length > 0) {
            changed = true;
            break;
          }
        }
      });

      return changed;
    }

    // Capture initial form state
    captureFormData();

    // Listen to form changes in all forms
    formsToTrack.forEach(form => {
      form.addEventListener('input', () => {
        formChanged = hasFormChanged();
      });

      form.addEventListener('change', () => {
        formChanged = hasFormChanged();
      });

      // Reset form tracking after successful save
      form.addEventListener('submit', () => {
        formChanged = false;
      });
    });

    // Tab click handler
    tabs.forEach(tab => {
      tab.addEventListener("click", function (e) {
        e.preventDefault();
        const targetTab = this.dataset.tab;
        
        // If clicking the already active tab, do nothing
        if (this.classList.contains('active')) {
          return;
        }

        pendingTab = targetTab;

        // Only show modal if form has changed
        if (formChanged) {
          tabSwitchModal.style.display = "flex";
        } else {
          switchTab(targetTab);
        }
      });
    });

    // Confirm button - switch tabs and reset tracking
    confirmBtn.addEventListener("click", () => {
      switchTab(pendingTab);
      tabSwitchModal.style.display = "none";
      
      // Reset form tracking after switching
      setTimeout(() => {
        captureFormData();
        formChanged = false;
      }, 100);
    });

    // Helper function to switch tabs
    function switchTab(tabName) {
      document.querySelectorAll(".tab-link").forEach(t => t.classList.remove("active"));
      document.querySelector(`.tab-link[data-tab="${tabName}"]`)?.classList.add("active");

      document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));
      document.getElementById(tabName)?.classList.add("active");
    }
  }

  // ================== PASSWORD UPDATE CONFIRMATION ==================
  (function () {
    const form = document.querySelector("form");
    const saveBtn = document.querySelector(".save-btn");
    const modal = document.getElementById("passwordConfirmModal");
    const confirmBtn = document.getElementById("confirmPasswordUpdate");

    if (!form || !saveBtn || !modal || !confirmBtn) return;

    let bypassModals = false;

    saveBtn.addEventListener("click", function (e) {
        if (bypassModals) return;

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
        bypassModals = true;
        form.requestSubmit ? form.requestSubmit(saveBtn) : form.submit();
    });
  })();

  // ---------- INITIALIZE ALL ----------
  setupDynamicModal("patientModal", "modal-body", ".openModalBtn", (btn) => btn.getAttribute("data-link"));
  setupModal("addEmployeeModal", "#addEmployeeBtn", ".close");
  setupDynamicModal("employeeModal", "employeeModalBody", ".schedule-btn, .edit-btn", (btn) => {
    const id = btn.getAttribute("data-id");
    const action = btn.classList.contains("schedule-btn") ? "schedule" : "edit";
    return `/clinic/employees/${id}/${action}`;
  });

  setupDeleteHandler(".delete-btn", (id) => `/clinic/employees/${id}`);
  setupImageModal();
  setupImageView("viewValidIdBtn", "validIdModal", "validIdImage", "closeModalBtn", "data-valid-id");
  setupImageView("viewValidIdBackBtn", "validIdModal", "validIdImage", "closeModalBtn", "data-valid-id");
  setupImageView("viewLicenseBtn", "licenseModal", "licenseImage", "closeLicenseBtn", "data-license");
  setupModal("forgotModal", ".openForgotBtn", ".close", "flex");
  setupModal("declineModal", ".openDeclineBtn", ".closeDeclineBtn", "block");
  setupModal("tabSwitchModal", ".openTabSwitchModal", ".closeTabSwitch", "flex");
  setupModal("passwordConfirmModal", ".openPasswordModal", ".closePasswordModal", "flex");
  setupDynamicModal("concernViewModal", "concernModalBody", ".openConcernModal", (btn) => btn.getAttribute("data-link") 
  );

  // Initialize tab switch modal with change detection
  setupTabSwitchModal();
});