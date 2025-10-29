document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatUsername = document.getElementById('chat-username');
    const chatList = document.getElementById('chat-list');
    const chatUserImage = document.querySelector('.chat-user img');

    let currentReceiverId = null;

    /** Load messages for the selected receiver */
    function loadMessages(receiverId) {
        fetch(`/messages/fetch?receiver_id=${receiverId}`)
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML = '';
                if (data.length === 0) {
                    chatMessages.innerHTML = `<p class="no-messages">No messages yet. Start the conversation!</p>`;
                } else {
                    data.forEach(msg => {
                        const div = document.createElement('div');
                        div.classList.add('message', msg.sender_id === window.userId ? 'sent' : 'received');
                        div.innerHTML = `<p>${msg.message}</p>`;
                        chatMessages.appendChild(div);
                    });
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    /** Handle chat selection from sidebar */
    chatList.addEventListener('click', e => {
        const chatItem = e.target.closest('.chat-item');
        if (!chatItem) return;

        const receiverId = chatItem.dataset.userId;
        const receiverName = chatItem.querySelector('h4').textContent;
        const receiverImage = chatItem.querySelector('img').src;

        currentReceiverId = receiverId;
        chatUsername.textContent = receiverName;
        chatUserImage.src = receiverImage;
        loadMessages(receiverId);
    });

    /** Send new message */
    chatForm.addEventListener('submit', e => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message === '' || !currentReceiverId) return;

        fetch("/messages/send", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ message, receiver_id: currentReceiverId })
        }).then(() => {
            messageInput.value = '';
        });
    });

    /** Initialize Pusher (real-time) */
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
    });

    const channel = pusher.subscribe('healconnect-chat');
    channel.bind('message.sent', function (data) {
        const msg = data.message;

        // Only append if message belongs to current chat
        if (
            msg.sender_id === window.userId && msg.receiver_id === currentReceiverId ||
            msg.receiver_id === window.userId && msg.sender_id === currentReceiverId
        ) {
            const div = document.createElement('div');
            div.classList.add('message', msg.sender_id === window.userId ? 'sent' : 'received');
            div.innerHTML = `<p>${msg.message}</p>`;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    /** Auto-open chat if ?receiver_id=### in URL */
    const urlParams = new URLSearchParams(window.location.search);
    const receiverIdFromUrl = urlParams.get('receiver_id');

    if (receiverIdFromUrl) {
        currentReceiverId = receiverIdFromUrl;
        // Try to find receiver in sidebar
        const chatItem = document.querySelector(`.chat-item[data-user-id="${receiverIdFromUrl}"]`);
        if (chatItem) {
            const receiverName = chatItem.querySelector('h4').textContent;
            const receiverImage = chatItem.querySelector('img').src;
            chatUsername.textContent = receiverName;
            chatUserImage.src = receiverImage;
        }
        loadMessages(receiverIdFromUrl);
    }
});
