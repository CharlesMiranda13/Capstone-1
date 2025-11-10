document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatUsername = document.getElementById('chat-username');
    const chatList = document.getElementById('chat-list');
    const chatUserImage = document.querySelector('.chat-user img');

    let currentReceiverId = null;
    let lastMessageDate = null; //track last message date shown

    /** format date label for each day */
    function getDateLabel(dateObj) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        const sameDay = (a, b) =>
            a.getFullYear() === b.getFullYear() &&
            a.getMonth() === b.getMonth() &&
            a.getDate() === b.getDate();

        if (sameDay(dateObj, today)) return "Today";
        if (sameDay(dateObj, yesterday)) return "Yesterday";

        return dateObj.toLocaleDateString([], {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    }

    /** Render a single message bubble (with date grouping) */
    function renderMessage(msg, isOwn) {
        const dateObj = new Date(msg.created_at || Date.now());
        const currentDate = dateObj.toDateString();

        // Add date header only once per day
        if (lastMessageDate !== currentDate) {
            const dateHeader = document.createElement('div');
            dateHeader.classList.add('date-divider');
            dateHeader.textContent = getDateLabel(dateObj);
            chatMessages.appendChild(dateHeader);
            lastMessageDate = currentDate;
        }

        const time = dateObj.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        const wrapper = document.createElement('div');
        wrapper.classList.add('message', isOwn ? 'sent' : 'received');

        wrapper.innerHTML = `
            <div class="bubble">
                <p class="text">${msg.message}</p>
                <span class="timestamp">${time}</span>
            </div>
        `;
        chatMessages.appendChild(wrapper);
    }

    /** Load messages for the selected receiver */
    function loadMessages(receiverId) {
        fetch(`/messages/fetch?receiver_id=${receiverId}`)
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML = '';
                lastMessageDate = null; // reset date tracking
                if (data.length === 0) {
                    chatMessages.innerHTML = `<p class="no-messages">No messages yet. Start the conversation!</p>`;
                } else {
                    data.forEach(msg => renderMessage(msg, msg.sender_id === window.userId));
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    /** Move chat to top of sidebar and update preview text */
    function moveChatToTop(userId, latestMessage) {
        const chatItem = chatList.querySelector(`.chat-item[data-user-id="${userId}"]`);
        if (chatItem) {
            const preview = chatItem.querySelector('.chat-info p');
            preview.textContent = latestMessage;
            chatList.prepend(chatItem);
        }
    }

    /** Handle chat selection from sidebar */
    chatList.addEventListener('click', e => {
        const chatItem = e.target.closest('.chat-item');
        if (!chatItem) return;

        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        chatItem.classList.add('active');

        const receiverId = chatItem.dataset.userId;
        const receiverName = chatItem.querySelector('h4').textContent;
        const receiverImage = chatItem.querySelector('img').src;

        currentReceiverId = receiverId;
        chatUsername.textContent = receiverName;
        chatUserImage.src = receiverImage;
        chatItem.classList.remove('unread');

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
        })
        .then(res => res.json())
        .then(data => {
            messageInput.value = '';
            renderMessage(data.message, true);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            moveChatToTop(currentReceiverId, data.message.message);
        });
    });

    /** Initialize Pusher (real-time) */
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }
    });

    const channel = pusher.subscribe(`private-healconnect-chat.${window.userId}`);

    channel.bind('message.sent', function (data) {
        const msg = data.message;
        if (msg.sender_id === window.userId) return;

        if (currentReceiverId == msg.sender_id) {
            renderMessage(msg, false);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
            const chatItem = document.querySelector(`.chat-item[data-user-id="${msg.sender_id}"]`);
            if (chatItem) chatItem.classList.add('unread');
        }

        moveChatToTop(msg.sender_id, msg.message);
    });

    /** Auto-open chat if ?receiver_id=### in URL */
    const urlParams = new URLSearchParams(window.location.search);
    const receiverIdFromUrl = urlParams.get('receiver_id');

    if (receiverIdFromUrl) {
        currentReceiverId = receiverIdFromUrl;
        const chatItem = document.querySelector(`.chat-item[data-user-id="${receiverIdFromUrl}"]`);
        if (chatItem) {
            const receiverName = chatItem.querySelector('h4').textContent;
            const receiverImage = chatItem.querySelector('img').src;
            chatUsername.textContent = receiverName;
            chatUserImage.src = receiverImage;
            chatItem.classList.add('active');
        }
        loadMessages(receiverIdFromUrl);
    }
});
