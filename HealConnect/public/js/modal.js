document.addEventListener("DOMContentLoaded", function() {

  // ----------------- PATIENT MODAL -----------------
  const patientModal = document.getElementById("patientModal");
  const patientModalBody = document.getElementById("modal-body");
  const patientCloseBtn = patientModal?.querySelector(".close");

  if (patientModal) {
    document.querySelectorAll(".openModalBtn").forEach(button => {
      button.addEventListener("click", function() {
        const link = this.getAttribute("data-link");

        fetch(link, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then(response => response.text())
          .then(html => {
            patientModalBody.innerHTML = html;
            patientModal.style.display = "block";
          })
          .catch(error => console.error("Error loading profile:", error));
      });
    });

    patientCloseBtn?.addEventListener("click", () => {
      patientModal.style.display = "none";
      patientModalBody.innerHTML = "";
    });

    window.addEventListener("click", (event) => {
      if (event.target === patientModal) {
        patientModal.style.display = "none";
        patientModalBody.innerHTML = "";
      }
    });
  }
  
   // ----------------- EMPLOYEE MODALS -----------------
  // ADD EMPLOYEE 
  const addEmployeeModal = document.getElementById("addEmployeeModal");
  const addEmployeeBtn = document.getElementById("addEmployeeBtn");
  const addEmployeeClose = addEmployeeModal?.querySelector(".close");

  if (addEmployeeModal) {
    addEmployeeBtn?.addEventListener("click", () => {
      addEmployeeModal.style.display = "block";
    });

    addEmployeeClose?.addEventListener("click", () => {
      addEmployeeModal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
      if (event.target === addEmployeeModal) {
        addEmployeeModal.style.display = "none";
      }
    });
  }

  // EMPLOYEE MODAL 
  const employeeModal = document.getElementById("employeeModal");
  const employeeModalBody = document.getElementById("employeeModalBody");
  const employeeClose = employeeModal?.querySelector(".close");

  if (employeeModal) {
    // Open employee modal for schedule or edit
    document.querySelectorAll(".schedule-btn, .edit-btn").forEach(button => {
      button.addEventListener("click", function() {
        const employeeId = this.getAttribute("data-id");
        let action = this.classList.contains("schedule-btn") ? "schedule" : "edit";
        const url = `/clinic/employees/${employeeId}/${action}`;

        fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then(response => response.text())
          .then(html => {
            employeeModalBody.innerHTML = html;
            employeeModal.style.display = "block";
          })
          .catch(error => console.error(`Error loading ${action}:`, error));
      });
    });

    employeeClose?.addEventListener("click", () => {
      employeeModal.style.display = "none";
      employeeModalBody.innerHTML = "";
    });

    window.addEventListener("click", (event) => {
      if (event.target === employeeModal) {
        employeeModal.style.display = "none";
        employeeModalBody.innerHTML = "";
      }
    });
  }

  //  DELETE EMPLOYEE 
  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", function() {
      const employeeId = this.getAttribute("data-id");

      if (confirm("Are you sure you want to delete this employee?")) {
        fetch(`/clinic/employees/${employeeId}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "X-Requested-With": "XMLHttpRequest"
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.closest("tr").remove();
          } else {
            alert("Failed to delete employee.");
          }
        })
        .catch(error => console.error("Error deleting employee:", error));
      }
    });
  });

});
