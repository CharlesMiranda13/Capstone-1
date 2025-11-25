document.addEventListener("DOMContentLoaded", function () {
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
  setupImageView("viewLicenseBtn", "licenseModal", "licenseImage", "closeLicenseBtn", "data-license");
  // Initialize Forgot Password Modal
  setupModal("forgotModal", ".openForgotBtn", ".close");


});
