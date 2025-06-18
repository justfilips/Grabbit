<x-layout title="Chat with {{ $user->name }}">
    <div class="container mt-5">
        <h2>Chat with {{ $user->name }}</h2>

        <div id="chat-box" style="border:1px solid #ccc; height:300px; overflow-y:scroll; padding:10px; margin-bottom:10px;">
        </div>

        <input type="text" id="chat-input" placeholder="Type your message..." style="width:80%;">
        <button id="send-button">Send</button>

        <script>
            const receiverId = {{ $user->id }};
            const chatBox = document.getElementById('chat-box');

            function loadMessages() {
                fetch('/messages/' + receiverId)
                    .then(response => response.json())
                    .then(messages => {
                        chatBox.innerHTML = '';
                        messages.forEach(msg => {
                            const msgDiv = document.createElement('div');
                            msgDiv.textContent = `[${msg.created_at}] ${msg.sender_id == {{ auth()->id() }} ? 'You' : 'User ' + msg.sender_id}: ${msg.message}`;
                            chatBox.appendChild(msgDiv);
                        });
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }

            loadMessages();

            document.getElementById('send-button').addEventListener('click', function() {
                const message = document.getElementById('chat-input').value;
                if (message.trim() === '') return;

                fetch('/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        receiver_id: receiverId,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(() => {
                    document.getElementById('chat-input').value = '';
                    loadMessages();
                });
            });

            // Optionally refresh messages every few seconds (simple solution for now)
            setInterval(loadMessages, 500);
        </script>
    </div>
</x-layout>
