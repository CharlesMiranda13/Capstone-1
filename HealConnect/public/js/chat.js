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
    const startVideoCall = document.getElementById("start-video-call");

    let currentReceiverId = null;
    let lastMessageDate = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let isRecording = false;
    let callStartTime = null;
    let callPopup = null;

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
        
        // Handle system messages (call ended)
        if (msg.type === 'system' || msg.message_type === 'system') {
            const wrapper = document.createElement('div');
            wrapper.classList.add('message', 'system-message');
            wrapper.dataset.messageId = msg.id;
            wrapper.innerHTML = `
                <div class="message-content">
                    <i class="fas fa-video"></i>
                    ${msg.message}
                </div>
            `;
            chatMessages.appendChild(wrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return;
        }

        // Regular messages
        const wrapper = document.createElement('div');
        wrapper.classList.add('message', isOwn ? 'sent' : 'received');
        wrapper.dataset.messageId = msg.id;

        let bubbleContent = '';
        if (msg.type === 'voice' || msg.message_type === 'voice') {
            bubbleContent = `
                <audio controls preload="none">
                    <source src="${msg.message_url || msg.message}" type="audio/webm">
                    Your browser does not support the audio element.
                </audio>
            `;
        } else if ((msg.type === 'file' || msg.message_type === 'file') && (msg.file_url || msg.message)) {
            const fileUrl = msg.file_url || msg.message;
            const ext = fileUrl.split('.').pop().toLowerCase();
            if (['jpg','jpeg','png','gif'].includes(ext)) {
                bubbleContent = `<img src="${fileUrl}" class="chat-file" alt="image">`;
            } else if (['mp4','mov','avi'].includes(ext)) {
                bubbleContent = `<video controls class="chat-file"><source src="${fileUrl}" type="video/${ext}"></video>`;
            } else {
                bubbleContent = `<a href="${fileUrl}" target="_blank">Download File</a>`;
            }
        } else {
            bubbleContent = `<p class="text">${msg.message}${msg.edited ? ' (edited)' : ''}</p>`;
        }

        // Menu for own messages
        let actions = '';
        if (isOwn) {
            actions = `
                <div class="message-menu">
                    <span class="menu-toggle">⋯</span>
                    <div class="menu-options">
                        ${(msg.type === 'text' || msg.message_type === 'text') ? `<span class="edit-message" data-message-id="${msg.id}">Edit</span>` : ''}
                        <span class="delete-message" data-message-id="${msg.id}">Delete</span>
                    </div>
                </div>
            `;
        }
        wrapper.innerHTML = `<div class="bubble">${bubbleContent}${actions}<span class="timestamp">${time}</span></div>`;
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

    /** ------------------ Update Unread Count in Chat List ------------------ */
    function updateChatListUnread(userId, unreadCount) {
        const chatItem = document.querySelector(`.chat-item[data-user-id="${userId}"]`);
        if (!chatItem) return;

        // Update data attribute
        chatItem.setAttribute('data-unread', unreadCount);

        let badge = chatItem.querySelector('.unread-badge');
        let dot = chatItem.querySelector('.unread-dot');
        const messagePreview = chatItem.querySelector('.chat-info p');
        const chatInfoHeader = chatItem.querySelector('.chat-info-header');

        if (unreadCount > 0) {
            chatItem.classList.add('has-unread');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'unread-badge';
                if (chatInfoHeader) {
                    chatInfoHeader.appendChild(badge);
                } else {
                    const h4 = chatItem.querySelector('.chat-info h4');
                    if (h4) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'chat-info-header';
                        h4.parentNode.insertBefore(wrapper, h4);
                        wrapper.appendChild(h4);
                        wrapper.appendChild(badge);
                    }
                }
            }
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;

            if (!dot) {
                dot = document.createElement('span');
                dot.className = 'unread-dot';
                chatItem.appendChild(dot);
            }

            if (messagePreview) {
                messagePreview.classList.add('unread-message');
            }
        } else {
            chatItem.classList.remove('has-unread');
            if (badge) badge.remove();
            if (dot) dot.remove();
            if (messagePreview) messagePreview.classList.remove('unread-message');
        }
    }

    function loadMessages(receiverId) {
        fetch(`/messages/fetch?receiver_id=${receiverId}`)
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML = '';
                lastMessageDate = null;
                if (data.length === 0) chatMessages.innerHTML = `<p class="no-messages">No messages yet. Start the conversation!</p>`;
                else data.forEach(msg => renderMessage(msg, msg.sender_id === window.userId));
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    function markMessagesAsRead(userId) {
        fetch(`/messages/mark-as-read/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateChatListUnread(userId, 0);
        })
        .catch(error => console.error('Error marking as read:', error));
    }

    // ---------------- Chat selection ----------------
    chatList.addEventListener('click', e => {
        const chatItem = e.target.closest('.chat-item');
        if (!chatItem) return;
        
        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        chatItem.classList.add('active');

        currentReceiverId = chatItem.dataset.userId;
        window.currentRecipientId = currentReceiverId;
        chatUsername.textContent = chatItem.querySelector('h4').textContent;
        chatUserImage.src = chatItem.querySelector('img').src;

        // Show/hide video call button based on selection
        if (startVideoCall) {
            startVideoCall.style.display = 'flex';
        }

        markMessagesAsRead(currentReceiverId);
        loadMessages(currentReceiverId);
    });

    // ---------------- Format call duration ----------------
    function formatCallDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        } else if (minutes > 0) {
            return `${minutes}m ${secs}s`;
        } else {
            return `${secs}s`;
        }
    }

    // ---------------- Handle call end ----------------
    async function handleCallEnd() {
        if (!callStartTime || !currentReceiverId) return;
        
        const callEndTime = new Date();
        const durationInSeconds = Math.floor((callEndTime - callStartTime) / 1000);
        
        // Only send message if call lasted at least 1 second
        if (durationInSeconds >= 1) {
            const formattedDuration = formatCallDuration(durationInSeconds);
            
            try {
                const response = await fetch('/messages/call-ended', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: currentReceiverId,
                        duration: formattedDuration
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Call ended message sent');
                }
            } catch (error) {
                console.error('Error sending call end message:', error);
            }
        }
        
        // Reset call tracking
        callStartTime = null;
        callPopup = null;
    }

    // ---------------- VIDEO CALL INITIATION ----------------
    if (startVideoCall) {
        startVideoCall.addEventListener('click', async () => {
            if (!currentReceiverId) {
                alert('Please select a conversation first.');
                return;
            }

            startVideoCall.disabled = true;
            const originalHTML = startVideoCall.innerHTML;
            startVideoCall.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Connecting...</span>';

            try {
                const response = await fetch('/video/create-room', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ receiver_id: currentReceiverId })
                });

                if (!response.ok) throw new Error('Server error: ' + response.status);

                const data = await response.json();

                if (data.success && data.redirect) {
                    // Record call start time
                    callStartTime = new Date();
                    
                    const width = 1280, height = 720;
                    const left = (screen.width - width) / 2;
                    const top = (screen.height - height) / 2;
                    
                    callPopup = window.open(
                        data.redirect,
                        'HealConnect_VideoCall',
                        `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no`
                    );

                    if (!callPopup) {
                        alert('Please allow popups and try again.');
                        callStartTime = null;
                    } else {
                        // Monitor when popup closes
                        const checkPopup = setInterval(() => {
                            if (callPopup.closed) {
                                clearInterval(checkPopup);
                                handleCallEnd();
                            }
                        }, 1000);
                    }
                } else {
                    throw new Error(data.message || 'Failed to create room');
                }
            } catch (error) {
                alert('Failed to start video call: ' + error.message);
                callStartTime = null;
            } finally {
                startVideoCall.disabled = false;
                startVideoCall.innerHTML = originalHTML;
            }
        });
    }

    // ---------------- File upload ----------------
    fileBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', async () => {
        if (!currentReceiverId || fileInput.files.length === 0) return;
        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('receiver_id', currentReceiverId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch('/messages/send-file', { method: 'POST', body: formData });
            const data = await res.json();
            renderMessage(data.message, true);
            moveChatToTop(currentReceiverId, `[File: ${file.name}]`);
        } catch (err) {
            console.error('File upload failed:', err);
            alert('Failed to send file.');
        } finally { fileInput.value = ''; }
    });

    // ---------------- Send text message ----------------
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
        } catch (err) { console.error("Text message send failed:", err); }
    });

    // ---------------- Voice recording ----------------
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
                            moveChatToTop(currentReceiverId, "[Voice Message]");
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

    /** ------------------ PUSHER SETUP ------------------ */
    console.log('=== INITIALIZING PUSHER ===');
    Pusher.logToConsole = true;

    const pusher = new Pusher(window.pusherKey, {
        cluster: window.pusherCluster,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: { 
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
            } 
        }
    });

    pusher.connection.bind('connected', () => console.log(' Pusher connected'));
    pusher.connection.bind('error', err => console.error(' Pusher error:', err));

    const channelName = `private-healconnect-chat.${window.userId}`;
    const channel = pusher.subscribe(channelName);

    channel.bind('pusher:subscription_succeeded', () => {
        console.log('Subscribed to:', channelName);
    });

    // Message sent event
    channel.bind('message.sent', data => {
        console.log(' Message received:', data);
        const msg = data.message;
        if (msg.sender_id === window.userId) return;

        if (currentReceiverId == msg.sender_id) {
            renderMessage(msg, false);
            markMessagesAsRead(msg.sender_id);
        } else {
            const chatItem = document.querySelector(`.chat-item[data-user-id="${msg.sender_id}"]`);
            if (chatItem) {
                const currentUnread = parseInt(chatItem.getAttribute('data-unread') || '0');
                updateChatListUnread(msg.sender_id, currentUnread + 1);
            }
        }
        
        const preview = msg.type === 'file' ? "[File]" : 
                       msg.type === 'voice' ? "[Voice Message]" : 
                       msg.type === 'system' ? msg.message :
                       msg.message;
        moveChatToTop(msg.sender_id, preview);
    });

    // ---------------- INCOMING VIDEO CALL EVENT ----------------
    channel.bind('video.call.started', data => {
        console.log('📞 Video call event received:', data);
        
        // CRITICAL: Verify this call is actually for THIS user
        if (data.receiver_id !== window.authUserId) {
            console.warn('⚠️ Call not intended for this user. Ignoring.', {
                intended_for: data.receiver_id,
                current_user: window.authUserId
            });
            return; // Exit early - this call is not for us
        }
        
        console.log('✅ Call confirmed for this user');
        
        const incomingCallDiv = document.getElementById('incoming-call');
        const callerNameSpan = document.getElementById('caller-name');
        const joinBtn = document.getElementById('join-call-btn');
        const declineBtn = document.getElementById('decline-call-btn');

        if (incomingCallDiv && callerNameSpan) {
            callerNameSpan.textContent = data.caller.name;
            incomingCallDiv.style.display = 'flex';

            const newJoinBtn = joinBtn.cloneNode(true);
            const newDeclineBtn = declineBtn.cloneNode(true);
            joinBtn.parentNode.replaceChild(newJoinBtn, joinBtn);
            declineBtn.parentNode.replaceChild(newDeclineBtn, declineBtn);

            newJoinBtn.addEventListener('click', () => {
                incomingCallDiv.style.display = 'none';
                
                // Record call start time for receiver
                callStartTime = new Date();
                
                const width = 1280, height = 720;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                const roomUrl = `/video/room/${data.room}${data.token ? '?token=' + data.token : ''}`;
                
                callPopup = window.open(
                    roomUrl,
                    'HealConnect_VideoCall',
                    `width=${width},height=${height},left=${left},top=${top},toolbar=no,menubar=no`
                );

                if (!callPopup) {
                    alert('Please allow popups.');
                    window.location.href = roomUrl;
                    callStartTime = null;
                } else {
                    currentReceiverId = data.caller.id;
                    
                    // Monitor when popup closes
                    const checkPopup = setInterval(() => {
                        if (callPopup.closed) {
                            clearInterval(checkPopup);
                            handleCallEnd();
                        }
                    }, 1000);
                }
            });

            newDeclineBtn.addEventListener('click', () => {
                console.log('📞 Call declined by user');
                incomingCallDiv.style.display = 'none';
                
                // Optional: Notify the caller that the call was declined
                // You can implement this if needed
            });
        }
    });
    // ---------------- Edit/Delete message ----------------
    chatMessages.addEventListener('click', e => {
        const target = e.target;
        if (target.classList.contains('menu-toggle')) 
            { const menu = target.nextElementSibling; menu.style.display = menu.style.display === 'block' ? 'none' : 'block'; return; }
        if (!target.classList.contains('edit-message') && !target.classList.contains('delete-message')) 
            document.querySelectorAll('.menu-options').forEach(menu => menu.style.display = 'none');

        if (target.classList.contains('edit-message')) {
            const messageId = target.dataset.messageId;
            const wrapper = chatMessages.querySelector(`.message[data-message-id="${messageId}"]`);
            const textElement = wrapper.querySelector('.text');
            const currentText = textElement.textContent.replace(' (edited)', '');
            const newMessage = prompt('Edit your message:', currentText);
            if (!newMessage) return;

            fetch(`/messages/${messageId}/edit`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: newMessage })
            })
            .then(res => res.json())
            .then(data => {
                textElement.textContent = data.message.message + ' (edited)';
                target.parentElement.style.display = 'none';
            })
            .catch(err => console.error('Edit failed:', err));
        }

        /** ------------------ DELETE MESSAGE ------------------ */
        if (target.classList.contains('delete-message')) {
            const messageId = target.dataset.messageId;
            if (!confirm('Are you sure you want to delete this message?')) return;

            fetch(`/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                const wrapper = chatMessages.querySelector(`.message[data-message-id="${messageId}"]`);
                const bubble = wrapper.querySelector('.bubble');
                let deletedText = 'This message was deleted.';
                if (wrapper.querySelector('audio')) deletedText = 'This voice message was deleted.';
                else if (wrapper.querySelector('img, video, a')) deletedText = 'This file was deleted.';
                bubble.innerHTML = `<p class="text">${deletedText}</p>`;
            })
            .catch(err => console.error('Delete failed:',err));
        }
    });

    document.addEventListener('click', e => { 
        if (!e.target.classList.contains('menu-toggle')) 
            document.querySelectorAll('.menu-options').forEach(menu => menu.style.display='none'); 
    });

    // ---------------- Auto-open chat via URL ----------------
    const urlParams = new URLSearchParams(window.location.search);
    const receiverIdFromUrl = urlParams.get('receiver_id');
    if (receiverIdFromUrl) {
        let chatItem = document.querySelector(`.chat-item[data-user-id="${receiverIdFromUrl}"]`);
        if (chatItem) chatItem.click();
        else {
            fetch(`/messages/user-info/${receiverIdFromUrl}`)
                .then(res => res.json())
                .then(user => {
                    const newChat = document.createElement('div');
                    newChat.classList.add('chat-item', 'active');
                    newChat.dataset.userId = user.id;
                    newChat.dataset.unread = '0';
                    newChat.innerHTML = `
                        <img src="${user.profile_picture ?? '/images/logo1.png'}" class="avatar" alt="User">
                        <div class="chat-info">
                            <div class="chat-info-header">
                                <h4>${user.name}</h4>
                            </div>
                            <p>Start a conversation...</p>
                        </div>
                    `;
                    chatList.prepend(newChat);
                    newChat.click();
                })
                .catch(err => console.error('Error fetching user info:', err));
        }
    }
});