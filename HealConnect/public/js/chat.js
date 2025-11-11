document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatUsername = document.getElementById('chat-username');
    const chatList = document.getElementById('chat-list');
    const chatUserImage = document.querySelector('.chat-user img');
    const recordBtn = document.getElementById('record-btn');
    const fileBtn = document.getElementById('file-btn');
    const fileInput = document.getElementById('file-input');

    let currentReceiverId = null;
    let lastMessageDate = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let isRecording = false;

    /** ------------------ Helper Functions ------------------ */
    function getDateLabel(dateObj) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);
        const sameDay = (a, b) => a.toDateString() === b.toDateString();
        if (sameDay(dateObj, today)) return "Today";
        if (sameDay(dateObj, yesterday)) return "Yesterday";
        return dateObj.toLocaleDateString([], { year: "numeric", month: "short", day: "numeric" });
    }

    function renderMessage(msg, isOwn) {
        const dateObj = new Date(msg.created_at || Date.now());
        const currentDate = dateObj.toDateString();

        if (lastMessageDate !== currentDate) {
            const dateHeader = document.createElement('div');
            dateHeader.classList.add('date-divider');
            dateHeader.textContent = getDateLabel(dateObj);
            chatMessages.appendChild(dateHeader);
            lastMessageDate = currentDate;
        }

        const time = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const wrapper = document.createElement('div');
        wrapper.classList.add('message', isOwn ? 'sent' : 'received');

        // Render based on type
        if (msg.type === 'voice') {
            wrapper.innerHTML = `
                <div class="bubble voice">
                    <audio controls preload="none">
                        <source src="${msg.message_url}" type="audio/webm">
                        Your browser does not support the audio element.
                    </audio>
                    <span class="timestamp">${time}</span>
                </div>
            `;
        } else if (msg.type === 'file' && msg.file_url) {
            const ext = msg.file_url.split('.').pop().toLowerCase();
            let content = '';
            if (['jpg','jpeg','png','gif'].includes(ext)) {
                content = `<img src="${msg.file_url}" class="chat-file" alt="image">`;
            } else if (['mp4','mov','avi'].includes(ext)) {
                content = `<video controls class="chat-file"><source src="${msg.file_url}" type="video/${ext}"></video>`;
            } else {
                content = `<a href="${msg.file_url}" target="_blank">Download File</a>`;
            }
            wrapper.innerHTML = `<div class="bubble">${content}<span class="timestamp">${time}</span></div>`;
        } else {
            wrapper.innerHTML = `
                <div class="bubble">
                    <p class="text">${msg.message}</p>
                    <span class="timestamp">${time}</span>
                </div>
            `;
        }

        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function moveChatToTop(userId, latestMessage) {
        const chatItem = chatList.querySelector(`.chat-item[data-user-id="${userId}"]`);
        if (chatItem) {
            const preview = chatItem.querySelector('.chat-info p');
            preview.textContent = latestMessage;
            chatList.prepend(chatItem);
        }
    }

    /** ------------------ Load Messages ------------------ */
    function loadMessages(receiverId) {
        fetch(`/messages/fetch?receiver_id=${receiverId}`)
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML = '';
                lastMessageDate = null;
                if (data.length === 0) {
                    chatMessages.innerHTML = `<p class="no-messages">No messages yet. Start the conversation!</p>`;
                } else {
                    data.forEach(msg => renderMessage(msg, msg.sender_id === window.userId));
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    /** ------------------ Chat Sidebar Selection ------------------ */
    chatList.addEventListener('click', e => {
        const chatItem = e.target.closest('.chat-item');
        if (!chatItem) return;
        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        chatItem.classList.add('active');
        currentReceiverId = chatItem.dataset.userId;
        chatUsername.textContent = chatItem.querySelector('h4').textContent;
        chatUserImage.src = chatItem.querySelector('img').src;
        chatItem.classList.remove('unread');
        loadMessages(currentReceiverId);
    });

    /** ------------------ File Upload ------------------ */
    fileBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', async () => {
        if (!currentReceiverId || fileInput.files.length === 0) return;
        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('receiver_id', currentReceiverId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch('/messages/send-file', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            renderMessage(data.message, true);
            moveChatToTop(currentReceiverId, `[File: ${file.name}]`);
        } catch (err) {
            console.error('File upload failed:', err);
            alert('Failed to send file.');
        } finally {
            fileInput.value = '';
        }
    });

    /** ------------------ Send Text Message ------------------ */
    chatForm.addEventListener('submit', async e => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message || !currentReceiverId) return;
        try {
            const res = await fetch("/messages/send", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message, receiver_id: currentReceiverId })
            });
            const data = await res.json();
            messageInput.value = '';
            renderMessage(data.message, true);
            moveChatToTop(currentReceiverId, data.message.message);
        } catch (err) {
            console.error("Text message send failed:", err);
        }
    });

    /** ------------------ Voice Recording ------------------ */
    if (recordBtn) {
        recordBtn.addEventListener('click', async () => {
            if (!currentReceiverId) return alert('Select a chat first.');
            if (!isRecording) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];
                    mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                    mediaRecorder.onstop = async () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        const audioFile = new File([audioBlob], `voice_${Date.now()}.webm`, { type: 'audio/webm' });

                        const formData = new FormData();
                        formData.append("voice_message", audioFile);
                        formData.append("receiver_id", currentReceiverId);
                        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

                        recordBtn.disabled = true;

                        try {
                            const res = await fetch("/messages/send-voice", { method: "POST", body: formData, headers: { 'Accept': 'application/json' } });
                            const data = await res.json();
                            renderMessage(data.message, true);
                            moveChatToTop(currentReceiverId, "[Voice Message 🎧]");
                        } catch (err) {
                            console.error("Voice upload failed:", err);
                            alert("Voice message failed.");
                        } finally { recordBtn.disabled = false; }
                    };

                    mediaRecorder.start();
                    recordBtn.classList.add("recording");
                    recordBtn.innerHTML = '<i class="fas fa-stop"></i>';
                    isRecording = true;
                    setTimeout(() => { if (isRecording) mediaRecorder.stop(); }, 60000);
                } catch { alert("Microphone access denied."); }
            } else {
                mediaRecorder.stop();
                recordBtn.classList.remove("recording");
                recordBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                isRecording = false;
            }
        });
    }

    /** ------------------ Real-time Updates (Pusher) ------------------ */
    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }
    });
    const channel = pusher.subscribe(`private-healconnect-chat.${window.userId}`);
    channel.bind('message.sent', data => {
        const msg = data.message;
        if (msg.sender_id === window.userId) return;

        if (currentReceiverId == msg.sender_id) {
            renderMessage(msg, false);
        } else {
            const chatItem = document.querySelector(`.chat-item[data-user-id="${msg.sender_id}"]`);
            if (chatItem) chatItem.classList.add('unread');
        }

        moveChatToTop(msg.sender_id, msg.type === 'voice' ? "[Voice Message 🎧]" : (msg.type === 'file' ? "[File]" : msg.message));
    });

    /** ------------------ Auto-open chat via URL ------------------ */
    const urlParams = new URLSearchParams(window.location.search);
    const receiverIdFromUrl = urlParams.get('receiver_id');
    if (receiverIdFromUrl) {
        const chatItem = document.querySelector(`.chat-item[data-user-id="${receiverIdFromUrl}"]`);
        if (chatItem) chatItem.click();
    }
});
