@extends('layouts.app')

@section('title', 'Chat dengan Admin')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content { padding: 20px; margin-top: 56px; }
        .chat-container { height: 80vh; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; background-color: #fff; }
        .chat-header { padding: 15px; background-color: #007bff; color: white; font-weight: 500; border-bottom: 1px solid #0056b3; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; background-color: #f1f3f5; }
        .message { margin-bottom: 15px; padding: 10px 15px; border-radius: 15px; max-width: 70%; position: relative; }
        .message.user { background-color: #007bff; color: white; margin-left: auto; text-align: right; }
        .message.admin { background-color: #e9ecef; color: black; margin-right: auto; text-align: left; }
        .message .time { font-size: 0.75rem; color: #adb5bd; margin-top: 5px; }
        .message.user .time { color: #d1e7ff; }
        .chat-input { padding: 15px; border-top: 1px solid #dee2e6; background-color: #fff; }
        .chat-input .form-control { border-radius: 20px; border: 1px solid #ced4da; }
        .chat-input .btn { border-radius: 20px; margin-left: 10px; }
        .alert { margin: 10px; }
        .footer { position: relative; bottom: 0; width: 100%; }
        @media (max-width: 768px) { .chat-container { height: 70vh; } }
    </style>
@endsection

@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('welcome') }}">Sistem Manajemen SIM</a>
        </div>
    </nav>

    <div class="content">
        <h2>Chat dengan Admin</h2>
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="chat-container">
            <div class="chat-header">
                Chat dengan Admin - {{ $sim->nomor_sim }}
            </div>
            <div class="chat-messages" id="chatMessages">
                @foreach ($messages as $message)
                    <div class="message {{ $message->sender_type === 'user' ? 'user' : 'admin' }}">
                        <strong>{{ $message->sender_type === 'user' ? 'Anda' : 'Admin' }}:</strong> {{ $message->message }}
                        <div class="time">{{ $message->created_at->format('H:i d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
            <div class="chat-input">
                <form id="chatForm">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" id="chatInput" placeholder="Ketik pesan..." required>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5 footer">
        <p>© {{ date('Y') }} Sistem Pengelolaan SIM. Hak Cipta Dilindungi.</p>
    </footer>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('chatInput').value.trim();
            const simNumber = '{{ $sim->nomor_sim }}';
            if (message) {
                console.log('Sending message:', { message, sim_number: simNumber });
                fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message, sim_number: simNumber })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        document.getElementById('chatInput').value = '';
                        const chatMessages = document.getElementById('chatMessages');
                        chatMessages.innerHTML += `
                            <div class="message user">
                                <strong>Anda:</strong> ${data.message.message}
                                <div class="time">${new Date(data.message.created_at).toLocaleString()}</div>
                            </div>
                        `;
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        alert('Gagal mengirim pesan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengirim pesan: Kesalahan koneksi.');
                });
            }
        });

        function loadMessages() {
            const simNumber = '{{ $sim->nomor_sim }}';
            console.log('Loading messages for:', simNumber);
            fetch(`{{ route("chat.get") }}?sim_number=${simNumber}`)
                .then(response => {
                    console.log('Fetch status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched messages:', data);
                    const chatMessages = document.getElementById('chatMessages');
                    chatMessages.innerHTML = data.map(msg => `
                        <div class="message ${msg.sender_type === 'user' ? 'user' : 'admin'}">
                            <strong>${msg.sender_type === 'user' ? 'Anda' : 'Admin'}:</strong> ${msg.message}
                            <div class="time">${new Date(msg.created_at).toLocaleString()}</div>
                        </div>
                    `).join('');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        setInterval(loadMessages, 3000);
        loadMessages();
    </script>
@endsection