// =========================
// Dynamic HTML Include Loader
// =========================
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll("[data-include]").forEach(el => {
    const file = el.getAttribute("data-include");
    fetch(file)
      .then(response => {
        if (!response.ok) throw new Error("File not found: " + file);
        return response.text();
      })
      .then(data => {
        el.innerHTML = data;
      })
      .catch(error => console.error(error));
  });
});

// =========================
// Hamburger Menu
// =========================
function toggleMenu() {
  document.getElementById("navbar").classList.toggle("show");
}

// =========================
// Tab Switching
// =========================
document.addEventListener("DOMContentLoaded", () => {
  const tabBtns = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  if (tabBtns.length > 0 && tabContents.length > 0) {
    tabBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        tabBtns.forEach(b => b.classList.remove("active"));
        tabContents.forEach(tc => tc.classList.remove("active"));
        btn.classList.add("active");
        document.getElementById(btn.dataset.tab).classList.add("active");
      });
    });
  }
});

// =========================
// Flip Card Animation
// =========================
document.addEventListener("DOMContentLoaded", () => {
  const flipCard = document.querySelector(".flip-card");
  if (flipCard) {
    flipCard.addEventListener("click", () => {
      flipCard.classList.toggle("flipped");
    });
  }
});

// =========================
// Card/Table View Toggle for Patients Page
// =========================
document.addEventListener("DOMContentLoaded", () => {
  const cardBtn = document.getElementById("cardViewBtn");
  const tableBtn = document.getElementById("tableViewBtn");
  const cardView = document.getElementById("cardView");
  const tableView = document.getElementById("tableView");

  // Prevent errors if not on the patients page
  if (!cardBtn || !tableBtn || !cardView || !tableView) return;

  cardBtn.addEventListener("click", () => {
    cardView.classList.remove("hidden");
    tableView.classList.add("hidden");
    cardBtn.classList.add("active");
    tableBtn.classList.remove("active");
  });

  tableBtn.addEventListener("click", () => {
    tableView.classList.remove("hidden");
    cardView.classList.add("hidden");
    tableBtn.classList.add("active");
    cardBtn.classList.remove("active");
  });
});
