@extends('layouts.app')

@section('title', 'Chat Admin')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container { display: flex; height: calc(100vh - 150px); margin-top: 20px; }
        .sim-list { width: 250px; background-color: #f8f9fa; border-right: 1px solid #dee2e6; overflow-y: auto; }
        .sim-list-header { padding: 10px; background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
        .sim-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #dee2e6; }
        .sim-item:hover, .sim-item.active { background-color: #e9ecef; }
        .sim-item .badge { font-size: 0.8em; }
        .chat-area { flex-grow: 1; display: flex; flex-direction: column; }
        .chat-header { padding: 10px; background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
        .chat-messages { flex-grow: 1; overflow-y: auto; padding: 10px; background-color: #fff; }
        .message { margin-bottom: 10px; padding: 8px 12px; border-radius: 5px; max-width: 70%; }
        .message.admin { background-color: #007bff; color: #fff; margin-left: auto; }
        .message.user { background-color: #e9ecef; }
        .message .time { font-size: 0.7em; color: #666; }
        .chat-input { padding: 10px; border-top: 1px solid #dee2e6; }
        .chat-input .input-group { width: 100%; }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <div class="sim-list">
            <div class="sim-list-header">
                <h5>Pemegang SIM</h5>
            </div>
            @forelse ($sims as $sim)
                <div class="sim-item {{ $currentSim == $sim->nomor_sim ? 'active' : '' }}" data-sim="{{ $sim->nomor_sim }}" onclick="selectSim('{{ $sim->nomor_sim }}')">
                    {{ $sim->nomor_sim }}
                    @if (isset($unreadCounts[$sim->nomor_sim]) && $unreadCounts[$sim->nomor_sim] > 0)
                        <span class="badge bg-danger float-end">{{ $unreadCounts[$sim->nomor_sim] }}</span>
                    @endif
                </div>
            @empty
                <div class="text-muted p-3">Belum ada Pemegang SIM.</div>
            @endforelse
        </div>
        <div class="chat-area">
            <div class="chat-header" id="chatHeader">Pilih Pemegang SIM untuk memulai chat</div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input">
                <form id="chatForm">
                    @csrf
                    <input type="hidden" name="sim_number" id="chatSimNumber">
                    <div class="input-group">
                        <input type="text" class="form-control" id="chatInput" placeholder="Ketik pesan..." disabled>
                        <button type="submit" class="btn btn-primary" disabled>Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        let currentSimNumber = null;

        // Inisialisasi Pusher
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            encrypted: true
        });

        function selectSim(simNumber) {
            currentSimNumber = simNumber;
            document.querySelectorAll('.sim-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`.sim-item[data-sim="${simNumber}"]`).classList.add('active');
            document.getElementById('chatInput').disabled = false;
            document.getElementById('chatInput').focus();
            document.querySelector('#chatForm button').disabled = false;
            document.getElementById('chatSimNumber').value = simNumber;
            document.getElementById('chatHeader').textContent = `Chat dengan ${simNumber}`;
            loadMessages();
            subscribeToChannel(simNumber);
        }

        function loadMessages() {
            if (!currentSimNumber) return;
            fetch(`/sim/get-messages?sim_number=${currentSimNumber}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(response => response.json())
            .then(messages => {
                const chatMessages = document.getElementById('chatMessages');
                chatMessages.innerHTML = messages.map(msg => {
                    const date = new Date(msg.created_at);
                    return `<div class="message ${msg.sender_type === 'admin' ? 'admin' : 'user'}">
                        <strong>${msg.sender_type === 'admin' ? 'Admin' : 'Pemegang SIM'}</strong>: ${msg.message}
                        <div class="time">${date.toLocaleString()}</div>
                    </div>`;
                }).join('');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

        function subscribeToChannel(simNumber) {
            const channel = pusher.subscribe(`private-chat.${simNumber}`);
            channel.bind('MessageSent', function(data) {
                loadMessages();
            });
        }

        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('chatInput').value.trim();
            if (message && currentSimNumber) {
                fetch('/sim/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message, sim_number: currentSimNumber })
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
        const savedSim = sessionStorage.getItem('current_sim');
        if (savedSim) selectSim(savedSim);
    </script>
@endsection