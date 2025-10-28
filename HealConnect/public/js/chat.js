document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');

    // Load all messages initially
    function loadMessages() {
        fetch("/messages/fetch")
            .then(res => res.json())
            .then(data => {
                chatMessages.innerHTML = '';
                data.forEach(msg => {
                    const div = document.createElement('div');
                    div.classList.add('message', msg.sender_id === window.userId ? 'sent' : 'received');
                    div.innerHTML = `<p>${msg.message}</p>`;
                    chatMessages.appendChild(div);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    //Send new message
    chatForm.addEventListener('submit', e => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message === '') return;

        fetch("/messages/send", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ message })
        }).then(() => {
            messageInput.value = '';
        });
    });

    //Pusher real-time listener
    const pusher = new Pusher(window.pusherKey, {
    cluster: window.pusherCluster,
    });

   
    pusher.logToConsole = true;

    const channel = pusher.subscribe('healconnect-chat');
    channel.bind('message.sent', function(data) {
        const div = document.createElement('div');
        div.classList.add('message', data.message.sender_id === window.userId ? 'sent' : 'received');
        div.innerHTML = `<p>${data.message.message}</p>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });

    
    loadMessages();
});
