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
        <div class="alert alert-success" data-translate>{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" data-translate>{{ session('error') }}</div>
    @endif

    {{ $slot }}

    @auth
    <div id="chat-widget">
        <div id="chat-toggle" style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; z-index: 9999;">
            <img src="https://static.vecteezy.com/system/resources/thumbnails/014/441/080/small_2x/chat-icon-design-in-blue-circle-png.png" alt="Chat" style="width: 50px; height: 50px;">
        </div>

        <div id="chat-panel" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 320px; height: 400px; background: white; border: 1px solid #ccc; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 9999;">
            <div id="chat-contacts" style="height: 100px; overflow-y: auto; border-bottom: 1px solid #ddd; padding: 5px;">
            </div>

            <div id="chat-messages" style="height: 240px; overflow-y: auto; padding: 10px; background: #f9f9f9;">
            </div>

            <div style="padding: 10px; border-top: 1px solid #ddd; display: flex;">
                <input id="chat-input" type="text" placeholder="Type a message..." style="flex-grow: 1; padding: 5px;">
                <button id="chat-send" style="padding: 5px 10px;">Send</button>
            </div>
        </div>
    </div>
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        const d = new Date();
        d.setTime(d.getTime() + days*24*60*60*1000);
        expires = "; expires=" + d.toUTCString();
      }
      document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      for(let i=0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
    }

    // main translate function that sets cookie
    async function translatePage(targetLang) {
        setCookie('siteLanguage', targetLang, 30);
        await doTranslate(targetLang);
    }

    // translate without resetting cookie
    async function doTranslate(targetLang) {
        const elements = [...document.querySelectorAll('[data-translate]')];

        const texts = elements.map(el => el.placeholder || el.textContent);

        const response = await fetch('/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                texts,
                target: targetLang
            })
        });

        const data = await response.json();

        data.translations.forEach((translated, i) => {
            if(elements[i].placeholder) {
                elements[i].placeholder = translated;
            } else {
                elements[i].textContent = translated;
            }
        });
    }

    // on page load, read cookie and apply language
    function initLanguage() {
        const savedLang = getCookie('siteLanguage') || 'en';
        if(savedLang !== 'en') {
            doTranslate(savedLang);
        }
    }

    window.addEventListener('DOMContentLoaded', initLanguage);

    const chatToggle = document.getElementById('chat-toggle');
    const chatPanel = document.getElementById('chat-panel');
    const chatContacts = document.getElementById('chat-contacts');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');

    let activeChatUserId = null;

    // toggle chat panel
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

    // load users chat contacts
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
                        startChat(contact.id, contact.name);
                    });

                    chatContacts.appendChild(contactDiv);
                });
            });
    }

    // load messages with user
    function loadMessages(userId) {
        fetch('/messages/' + userId)
            .then(response => response.json())
            .then(messages => {
                chatMessages.innerHTML = '';
                messages.forEach(msg => {
                    const msgDiv = document.createElement('div');
                    const sender = msg.sender_id === {{ auth()->id() }} ? 'You' : 'Them';
                    msgDiv.textContent = `[${msg.created_at}] ${sender}: ${msg.message}`;
                    chatMessages.appendChild(msgDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    // send message
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
        }).then(() => {
            chatInput.value = '';
            loadMessages(activeChatUserId);
            loadContacts();
        });
    });

    // triggered from outside to start chat with someone
    function startChat(userId, userName) {
        chatPanel.style.display = 'block';
        activeChatUserId = userId;
        loadContacts();
        loadMessages(userId);
    }
    </script>
</body>
</html>
