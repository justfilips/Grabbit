<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
<x-navbar />

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{ $slot }}

@auth
<div id="chat-widget">

    <div id="chat-toggle"
         style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; z-index: 9999;">
        <img src="https://static.vecteezy.com/system/resources/thumbnails/014/441/080/small_2x/chat-icon-design-in-blue-circle-png.png"
             style="width: 50px; height: 50px;">
    </div>

    <div id="chat-panel"
         style="display:none; position:fixed; bottom:80px; right:20px; width:320px; height:400px;
                background:white; border:1px solid #ccc; border-radius:10px;
                overflow:hidden; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:9999;">

        <div id="chat-contacts"
             style="height:100px; overflow-y:auto; border-bottom:1px solid #ddd; padding:5px;">
        </div>

        <div id="chat-messages"
             style="height:240px; overflow-y:auto; padding:10px; background:#f9f9f9; display:flex; flex-direction:column;">
        </div>

        <div style="padding:10px; border-top:1px solid #ddd; display:flex;">
            <input id="chat-input" type="text" placeholder="Type a message..."
                   style="flex-grow:1; padding:5px;">
            <button id="chat-send" style="padding:5px 10px;">Send</button>
        </div>
    </div>
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chatToggle = document.getElementById('chat-toggle');
    const chatPanel = document.getElementById('chat-panel');
    const chatContacts = document.getElementById('chat-contacts');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');

    if (!chatToggle || !chatPanel) return;

    let activeChatUserId = null;
    const currentUserId = {{ auth()->check() ? auth()->id() : 'null' }};

    // TOGGLE
    chatToggle.addEventListener('click', () => {
        const open = chatPanel.style.display === 'block';

        if (open) {
            chatPanel.style.display = 'none';
            chatMessages.innerHTML = '';
            chatContacts.innerHTML = '';
            activeChatUserId = null;
        } else {
            chatPanel.style.display = 'block';
            loadContacts();
        }
    });

    // CONTACTS
    function loadContacts() {
        fetch('/chat-contacts')
            .then(r => r.json())
            .then(contacts => {
                chatContacts.innerHTML = '';

                contacts.forEach(contact => {
                    const div = document.createElement('div');
                    div.textContent = contact.name;
                    div.style.cursor = 'pointer';
                    div.style.padding = '5px';
                    div.style.borderBottom = '1px solid #eee';

                    div.onclick = () => startChat(contact.id, contact.name);

                    chatContacts.appendChild(div);
                });
            });
    }

    // MESSAGES (🔥 FIXED UI HERE)
    function loadMessages(userId) {
        fetch('/messages/' + userId)
            .then(r => r.json())
            .then(messages => {

                chatMessages.innerHTML = '';

                messages.forEach(msg => {

                    const isMe = msg.sender_id === currentUserId;

                    const time = new Date(msg.created_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const div = document.createElement('div');

                    div.style.maxWidth = "80%";
                    div.style.marginBottom = "8px";
                    div.style.padding = "8px 10px";
                    div.style.borderRadius = "10px";
                    div.style.alignSelf = isMe ? "flex-end" : "flex-start";
                    div.style.background = isMe ? "#d1e7ff" : "#eee";

                    div.innerHTML = `
                        <div style="font-size:11px; color:#666; margin-bottom:3px;">
                            <strong>${isMe ? 'You' : 'Them'}</strong> • ${time}
                        </div>
                        <div>${msg.message}</div>
                    `;

                    chatMessages.appendChild(div);
                });

                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    // SEND
    chatSend.addEventListener('click', () => {

        if (!activeChatUserId) {
            alert('Select a user first');
            return;
        }

        const message = chatInput.value.trim();
        if (!message) return;

        fetch('/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                receiver_id: activeChatUserId,
                message: message
            })
        })
        .then(() => {
            chatInput.value = '';
            loadMessages(activeChatUserId);
        });
    });

    // GLOBAL CHAT OPEN
    window.startChat = function (userId, userName) {
        chatPanel.style.display = 'block';
        activeChatUserId = userId;

        loadContacts();
        loadMessages(userId);
    };

});
</script>

</body>
</html>