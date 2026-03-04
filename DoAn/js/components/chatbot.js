// ==================== CHATBOT COMPONENT ====================

// Initialize chatbot
function initChatbot() {
  const chatbotToggle = document.getElementById('chatbot-toggle');
  const chatbot = document.getElementById('chatbot');
  const chatbotClose = document.getElementById('chatbot-close');
  const chatbotSend = document.getElementById('chatbot-send');
  const chatbotInput = document.getElementById('chatbot-input');
  const messagesContainer = document.getElementById('chatbot-messages');

  chatbotToggle.addEventListener('click', () => {
    chatbot.classList.toggle('active');
  });

  chatbotClose.addEventListener('click', () => {
    chatbot.classList.remove('active');
  });

  function sendMessage() {
    const message = chatbotInput.value.trim();
    if (!message) return;

    // Add user message
    const userMsg = document.createElement('div');
    userMsg.className = 'chatbot-message user';
    userMsg.innerHTML = `<p>${message}</p>`;
    messagesContainer.appendChild(userMsg);
    chatbotInput.value = '';

    // Simulate bot response
    setTimeout(() => {
      const botMsg = document.createElement('div');
      botMsg.className = 'chatbot-message bot';
      
      const responses = [
        'Cảm ơn bạn đã liên hệ! Tôi có thể giúp gì cho bạn?',
        'Bạn có thể tìm kiếm sách theo tên, tác giả hoặc thể loại.',
        'Để xem giỏ hàng, hãy nhấn vào biểu tượng giỏ hàng ở góc trên.',
        'Chúng tôi có chương trình khuyến mãi đặc biệt vào cuối tuần này!',
        'Bạn cần hỗ trợ về đơn hàng nào? Tôi sẽ giúp bạn kiểm tra.'
      ];
      
      botMsg.innerHTML = `<p>${responses[Math.floor(Math.random() * responses.length)]}</p>`;
      messagesContainer.appendChild(botMsg);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 1000);
  }

  chatbotSend.addEventListener('click', sendMessage);
  chatbotInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
  });
}

// Export
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initChatbot };
}

