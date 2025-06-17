<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }}</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <x-navbar />
  {{ $slot }}
    
  <!-- Chat Widget -->
  <div id="chat-widget">
      <!-- Chat Toggle Button -->
      <div id="chat-toggle" style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; z-index: 9999;">
          <img src="https://static.vecteezy.com/system/resources/thumbnails/014/441/080/small_2x/chat-icon-design-in-blue-circle-png.png" alt="Chat" style="width: 50px; height: 50px;">
      </div>

      <!-- Chat Panel -->
      <div id="chat-panel" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 320px; height: 400px; background: white; border: 1px solid #ccc; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 9999;">
          <!-- Contacts list -->
          <div id="chat-contacts" style="height: 100px; overflow-y: auto; border-bottom: 1px solid #ddd; padding: 5px;">
              <!-- Contacts will load here -->
          </div>
          
          <!-- Chat messages -->
          <div id="chat-messages" style="height: 240px; overflow-y: auto; padding: 10px; background: #f9f9f9;">
              <!-- Messages will load here -->
          </div>
          
          <!-- Input -->
          <div style="padding: 10px; border-top: 1px solid #ddd; display: flex;">
              <input id="chat-input" type="text" placeholder="Type a message..." style="flex-grow: 1; padding: 5px;">
              <button id="chat-send" style="padding: 5px 10px;">Send</button>
          </div>
      </div>
  </div>

    <!-- Bootstrap JS CDN (optional, only if you need Bootstrap JS features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
    const chatToggle = document.getElementById('chat-toggle');
    const chatPanel = document.getElementById('chat-panel');
    const chatContacts = document.getElementById('chat-contacts');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');

    let activeChatUserId = null;

    // Toggle chat panel
    chatToggle.addEventListener('click', () => {
        if (chatPanel.style.display === 'none' || chatPanel.style.display === '') {
            chatPanel.style.display = 'block';
            loadContacts();
        } else {
            chatPanel.style.display = 'none';
            chatMessages.innerHTML = '';
            chatContacts.innerHTML = '';
            activeChatUserId = null;
        }
    });

    // Load contacts (get users you have messaged with)
    function loadContacts() {
        fetch('/chat-contacts')
            .then(response => response.json())
            .then(contacts => {
                chatContacts.innerHTML = '';
                contacts.forEach(contact => {
                    const contactDiv = document.createElement('div');
                    contactDiv.textContent = contact.name;
                    contactDiv.style.cursor = 'pointer';
                    contactDiv.style.padding = '5px';
                    contactDiv.style.borderBottom = '1px solid #eee';

                    contactDiv.addEventListener('click', () => {
                        activeChatUserId = contact.id;
                        loadMessages(activeChatUserId);
                    });

                    chatContacts.appendChild(contactDiv);
                });
            });
    }

    // Load messages with selected user
    function loadMessages(userId) {
        fetch('/messages/' + userId)
            .then(response => response.json())
            .then(messages => {
                chatMessages.innerHTML = '';
                messages.forEach(msg => {
                    const msgDiv = document.createElement('div');
                    msgDiv.textContent = `[${msg.created_at}] ${msg.sender_id === {{ auth()->id() }} ? 'You' : 'User ' + msg.sender_id}: ${msg.message}`;
                    chatMessages.appendChild(msgDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    // Send message
    chatSend.addEventListener('click', () => {
        if (!activeChatUserId) {
            alert('Please select a contact to chat with.');
            return;
        }

        const message = chatInput.value.trim();
        if (!message) return;

        fetch('/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                receiver_id: activeChatUserId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            chatInput.value = '';
            loadMessages(activeChatUserId);
        });
    });
</script>
