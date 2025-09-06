document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".speak-btn").forEach(button => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target");
      const targetElement = document.getElementById(targetId);

      if (!targetElement) {
        console.error("Target element not found:", targetId);
        return;
      }

      // If already speaking → stop
      if (window.speechSynthesis.speaking) {
        window.speechSynthesis.cancel();
        return;
      }

      // Get the text
      const text = targetElement.innerText.trim();
      if (!text) {
        console.error("No text to speak for:", targetId);
        return;
      }

      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = "en-US"; 
      utterance.rate = 1;
      utterance.pitch = 1;

    // Speak
      window.speechSynthesis.cancel();
      window.speechSynthesis.speak(utterance);
    });
  });
});
