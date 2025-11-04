document.addEventListener("DOMContentLoaded", function() {
  const modal = document.getElementById("patientModal");
  const modalBody = document.getElementById("modal-body");
  const closeBtn = document.querySelector(".close");

  document.querySelectorAll(".openModalBtn").forEach(button => {
    button.addEventListener("click", function() {
      const link = this.getAttribute("data-link");

      fetch(link, {
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
      .then(response => response.text())
      .then(html => {
        modalBody.innerHTML = html;
        modal.style.display = "block";
      })
      .catch(error => console.error("Error loading profile:", error));
    });
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
    modalBody.innerHTML = "";
  });

  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
      modalBody.innerHTML = "";
    }
  });

  buttons.forEach(button => {
  button.addEventListener("click", function() {
    const link = this.getAttribute("data-link");
    iframe.src = link + "?embed=true"; 
    modal.style.display = "block";
  });
})
});
