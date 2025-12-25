/**
 * LaporWarga Chatbot - Client-side Logic
 * Handles UI interactions and API communication
 */

(function () {
  "use strict";

  // DOM Elements
  const chatbotToggle = document.getElementById("chatbot-toggle");
  const chatbotPanel = document.getElementById("chatbot-panel");
  const chatbotClose = document.getElementById("chatbot-close");
  const chatbotInput = document.getElementById("chatbot-input");
  const chatbotSend = document.getElementById("chatbot-send");
  const chatbotMessages = document.getElementById("chatbot-messages");
  const chatbotTyping = document.getElementById("chatbot-typing");

  if (!chatbotToggle || !chatbotPanel) {
    return; // Chatbot not present on this page
  }

  // State
  let isOpen = false;
  let isSending = false;

  // Toggle chat panel
  function toggleChat() {
    isOpen = !isOpen;
    chatbotPanel.classList.toggle("active", isOpen);

    if (isOpen) {
      chatbotInput.focus();
    }
  }

  // Add message to chat
  function addMessage(text, isUser = false) {
    const messageDiv = document.createElement("div");
    messageDiv.className = `chatbot-message ${isUser ? "user-message" : "bot-message"}`;

    const avatar = document.createElement("div");
    avatar.className = "message-avatar";
    avatar.innerHTML = isUser ? '<i class="ri-user-line"></i>' : '<i class="ri-robot-line"></i>';

    const content = document.createElement("div");
    content.className = "message-content";

    const paragraph = document.createElement("p");
    paragraph.textContent = text;

    content.appendChild(paragraph);
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(content);

    chatbotMessages.appendChild(messageDiv);

    // Scroll to bottom
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
  }

  // Show typing indicator
  function showTyping(show = true) {
    chatbotTyping.style.display = show ? "block" : "none";
    if (show) {
      chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
  }

  // Send message to API
  async function sendMessage(message) {
    if (isSending || !message.trim()) {
      return;
    }

    isSending = true;
    chatbotSend.disabled = true;
    chatbotInput.disabled = true;

    // Add user message
    addMessage(message, true);
    chatbotInput.value = "";

    // Show typing indicator
    showTyping(true);

    try {
      const response = await fetch("../api/chatbot.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ message: message }),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        // Add bot response
        addMessage(data.message);
      } else {
        // Show error message
        const errorMsg = data.error || "Terjadi kesalahan. Silakan coba lagi.";
        addMessage("⚠️ " + errorMsg);
      }
    } catch (error) {
      console.error("Chatbot error:", error);
      addMessage("⚠️ Koneksi gagal. Silakan periksa internet Anda dan coba lagi.");
    } finally {
      showTyping(false);
      isSending = false;
      chatbotSend.disabled = false;
      chatbotInput.disabled = false;
      chatbotInput.focus();
    }
  }

  // Handle suggestion clicks
  function handleSuggestion(event) {
    if (event.target.classList.contains("suggestion-btn")) {
      const message = event.target.getAttribute("data-msg");
      if (message) {
        chatbotInput.value = message;
        sendMessage(message);
      }
    }
  }

  // Event Listeners
  chatbotToggle.addEventListener("click", toggleChat);
  chatbotClose.addEventListener("click", toggleChat);

  chatbotSend.addEventListener("click", function () {
    sendMessage(chatbotInput.value);
  });

  chatbotInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter" && !event.shiftKey) {
      event.preventDefault();
      sendMessage(chatbotInput.value);
    }
  });

  // Handle suggestion button clicks
  chatbotMessages.addEventListener("click", handleSuggestion);

  // Close panel when clicking outside
  document.addEventListener("click", function (event) {
    if (isOpen && !chatbotPanel.contains(event.target) && !chatbotToggle.contains(event.target)) {
      toggleChat();
    }
  });

  // Prevent panel close when clicking inside
  chatbotPanel.addEventListener("click", function (event) {
    event.stopPropagation();
  });

  console.log("Chatbot initialized successfully");
})();
