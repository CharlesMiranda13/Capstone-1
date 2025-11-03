document.addEventListener("DOMContentLoaded", function() {
  const modal = document.getElementById("patientModal");
  const iframe = document.getElementById("profileFrame");
  const closeBtn = document.querySelector(".close");

  const buttons = document.querySelectorAll(".openModalBtn");

  buttons.forEach(button => {
    button.addEventListener("click", function() {
      const link = this.getAttribute("data-link");
      iframe.src = link; 
      modal.style.display = "block";
    });
  });

  // Close the modal when clicking the X
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
    iframe.src = ""; 
  });

  // Close the modal when clicking outside
  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
      iframe.src = "";
    }
  });
});
