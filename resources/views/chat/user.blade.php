@extends('layouts.app')

@section('title', 'Chat User')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container { max-width: 600px; margin: 20px auto; }
        .chat-header { padding: 10px; background-color: #e9ecef; border-bottom: 1px solid #dee2e6; text-align: center; }
        .chat-messages { height: 400px; overflow-y: auto; padding: 10px; background-color: #fff; }
        .message { margin-bottom: 10px; padding: 8px 12px; border-radius: 5px; max-width: 70%; }
        .message.admin { background-color: #007bff; color: #fff; }
        .message.user { background-color: #e9ecef; margin-left: auto; }
        .message .time { font-size: 0.7em; color: #666; }
        .chat-input { padding: 10px; border-top: 1px solid #dee2e6; }
        .chat-input .input-group { width: 100%; }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <div class="chat-header">Chat dengan Admin (SIM: {{ $sim->nomor_sim }})</div>
        <div class="chat-messages" id="chatMessages">
            @forelse ($messages as $message)
                <div class="message {{ $message->sender_type === 'admin' ? 'admin' : 'user' }}">
                    <strong>{{ $message->sender_type === 'admin' ? 'Admin' : 'Anda' }}</strong>: {{ $message->message }}
                    <div class="time">{{ $message->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
            @empty
                <div class="text-muted text-center p-3">Belum ada pesan.</div>
            @endforelse
        </div>
        <div class="chat-input">
            <form id="chatForm">
                @csrf
                <input type="hidden" name="sim_number" value="{{ $sim->nomor_sim }}">
                <div class="input-group">
                    <input type="text" class="form-control" id="chatInput" placeholder="Ketik pesan..." required>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        const simNumber = '{{ $sim->nomor_sim }}';
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            encrypted: true
        });

        const channel = pusher.subscribe(`private-chat.${simNumber}`);
        channel.bind('MessageSent', function(data) {
            loadMessages();
        });

        function loadMessages() {
            fetch(`/sim/get-messages?sim_number=${simNumber}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(response => response.json())
            .then(messages => {
                const chatMessages = document.getElementById('chatMessages');
                chatMessages.innerHTML = messages.map(msg => {
                    const date = new Date(msg.created_at);
                    return `<div class="message ${msg.sender_type === 'admin' ? 'admin' : 'user'}">
                        <strong>${msg.sender_type === 'admin' ? 'Admin' : 'Anda'}</strong>: ${msg.message}
                        <div class="time">${date.toLocaleString()}</div>
                    </div>`;
                }).join('');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('chatInput').value.trim();
            if (message) {
                fetch('/sim/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message, sim_number: simNumber })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('chatInput').value = '';
                        loadMessages();
                    } else {
                        alert('Gagal mengirim pesan: ' + data.message);
                    }
                });
            }
        });

        setInterval(loadMessages, 3000);
        loadMessages();
    </script>
@endsection