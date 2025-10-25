document.addEventListener('DOMContentLoaded', () => {
    const sendBtn = document.getElementById('sendBtn');
    const input = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');

    sendBtn.addEventListener('click', () => {
        const message = input.value.trim();
        if (message !== '') {
            const msgContainer = document.createElement('div');
            msgContainer.classList.add('message', 'sent');
            msgContainer.innerHTML = `<div class="bubble">${message}</div>`;
            chatMessages.appendChild(msgContainer);
            input.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendBtn.click();
    });
});
