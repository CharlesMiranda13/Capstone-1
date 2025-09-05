document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("adminLoginForm");

  if (form) {
    form.addEventListener("submit", function(event) {
      event.preventDefault();

      const user = document.getElementById("username").value;
      const pass = document.getElementById("password").value;

      if (user === "admin" && pass === "1234") {
        window.location.href = "/user/admin";
      } else {
        alert("Invalid credentials! Try again.");
      }
    });
  }
});
  