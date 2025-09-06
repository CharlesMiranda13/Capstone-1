document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".speak-btn").forEach(button => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target");
      const targetElement = document.getElementById(targetId);

      if (!targetElement) {
        console.error("Target element not found:", targetId);
        return;
      }

      const text = targetElement.innerText.trim();
      if (!text) {
        console.error("No text to speak for:", targetId);
        return;
      }

      // Debug
      console.log("Speaking:", text);

      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = "en-US"; // change to "fil-PH" later for Tagalog
      utterance.rate = 1;
      utterance.pitch = 1;

      window.speechSynthesis.cancel(); // stop any ongoing speech
      window.speechSynthesis.speak(utterance);
    });
  });
});
