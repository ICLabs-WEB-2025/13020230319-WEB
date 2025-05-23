@extends('layouts.app')

@section('title', 'Chat Admin')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet" id="chatCssLink">
    <script>console.log('CSS loaded from:', '{{ asset('css/chat.css') }}');</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <style>
        .text-shadow { text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3); }
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Sistem Manajemen SIM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3"><i class="fas fa-user-circle"></i> Selamat datang, {{ Auth::user()->email }}</span>
                    </li>
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <h6 class="text-center mb-4"><i class="fas fa-bars me-2"></i>Menu</h6>
        <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="{{ route('sim.create') }}"><i class="fas fa-plus-circle me-2"></i>Tambah SIM</a>
        <a href="{{ route('chat.index') }}" class="active"><i class="fas fa-comments me-2"></i>Chat</a>
    </div>

    <div class="content">
        <h2 class="animate__animated animate__fadeIn text-shadow"><i class="fas fa-comments me-2"></i> Chat dengan Pemegang SIM</h2>
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInUp" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="chat-container">
            <div class="sim-list">
                <div class="sim-list-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Pemegang SIM</h5>
                </div>
                @forelse ($sims as $sim)
                    <div class="sim-item {{ $currentSim == $sim->nomor_sim ? 'active' : '' }}" data-sim="{{ $sim->nomor_sim }}" onclick="selectSim('{{ $sim->nomor_sim }}')">
                        {{ $sim->nomor_sim }}
                        @if (isset($unreadCounts[$sim->nomor_sim]) && $unreadCounts[$sim->nomor_sim] > 0)
                            <span class="badge bg-danger float-end new">{{ $unreadCounts[$sim->nomor_sim] }}</span>
                        @endif
                    </div>
                @empty
                    <div class="text-muted p-3">Belum ada Pemegang SIM.</div>
                @endforelse
            </div>
            <div class="chat-area">
                <div class="chat-header" id="chatHeaderTitle">
                    Pilih Pemegang SIM untuk memulai chat
                    <span class="clear-session" onclick="clearSimSession()" style="display: none;" id="clearSessionBtn">Hapus Sesi</span>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="text-muted text-center p-3">Pilih Pemegang SIM untuk memulai chat.</div>
                </div>
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
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/chat.js') }}" id="chatJsScript"></script>
    <script>console.log('JS loaded from:', '{{ asset('js/chat.js') }}');</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        // Pastikan variabel global didefinisikan
        let currentSimNumber = null;
        let lastMessageCount = 0;

        function selectSim(simNumber) {
            currentSimNumber = simNumber;
            sessionStorage.setItem('current_sim', simNumber);
            document.querySelectorAll('.sim-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`.sim-item[data-sim="${simNumber}"]`).classList.add('active');
            document.getElementById('chatInput').disabled = false;
            document.getElementById('chatInput').focus();
            document.querySelector('#chatForm button').disabled = false;
            document.getElementById('chatSimNumber').value = simNumber;
            document.getElementById('chatHeaderTitle').innerHTML = `Chat dengan ${simNumber} <span class="clear-session" onclick="clearSimSession()" id="clearSessionBtn">Hapus Sesi</span>`;
            document.getElementById('clearSessionBtn').style.display = 'inline';
            loadMessages();
            console.log('Selected SIM:', simNumber);
        }

        function clearSimSession() {
            currentSimNumber = null;
            sessionStorage.removeItem('current_sim');
            document.querySelectorAll('.sim-item').forEach(item => item.classList.remove('active'));
            document.getElementById('chatInput').disabled = true;
            document.querySelector('#chatForm button').disabled = true;
            document.getElementById('chatSimNumber').value = '';
            document.getElementById('chatHeaderTitle').innerHTML = 'Pilih Pemegang SIM untuk memulai chat';
            document.getElementById('clearSessionBtn').style.display = 'none';
            document.getElementById('chatMessages').innerHTML = '<div class="text-muted text-center p-3">Pilih Pemegang SIM untuk memulai chat.</div>';
        }

        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('chatInput').value.trim();
            const simNumber = document.getElementById('chatSimNumber').value;
            if (message && simNumber) {
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
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        document.getElementById('chatInput').value = '';
                        loadMessages();
                    } else {
                        alert('Gagal mengirim pesan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengirim pesan: ' + error.message);
                });
            }
        });

        function loadMessages() {
            if (!currentSimNumber) return;
            console.log('Loading messages for:', currentSimNumber);
            fetch(`{{ route("chat.get") }}?sim_number=${currentSimNumber}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Fetch status:', response.status);
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Fetched messages:', data);
                const chatMessages = document.getElementById('chatMessages');
                const newMessages = data.map(msg => {
                    const date = new Date(msg.created_at);
                    return `
                        <div class="message ${msg.sender_type === 'admin' ? 'admin' : 'user'}">
                            <strong>${msg.sender_type === 'admin' ? 'Admin' : 'Pemegang SIM'}:</strong> ${e(msg.message)}
                            <div class="time">${date.toLocaleString()}</div>
                        </div>
                    `;
                }).join('');
                chatMessages.innerHTML = newMessages;
                if (data.length > lastMessageCount) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    lastMessageCount = data.length;
                }
            })
            .catch(error => console.error('Error loading messages:', error));
        }

        function e(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        setInterval(loadMessages, 3000);
        const savedSim = sessionStorage.getItem('current_sim');
        if (savedSim) selectSim(savedSim);
    </script>
@endsection