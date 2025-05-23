<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Admin - Sistem Manajemen SIM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .navbar { box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .sidebar { 
            height: 100vh; 
            width: 250px; 
            position: fixed; 
            top: 56px; 
            left: 0; 
            background-color: #2c3e50; 
            padding-top: 20px; 
            color: #fff; 
            transition: width 0.3s; 
        }
        .sidebar a { 
            color: #ecf0f1; 
            padding: 10px 20px; 
            display: block; 
            text-decoration: none; 
        }
        .sidebar a:hover { background-color: #34495e; }
        .sidebar a.active { background-color: #3498db; }
        .content { 
            margin-left: 250px; 
            padding: 30px; 
            margin-top: 56px; 
            min-height: calc(100vh - 56px); 
        }
        .chat-container { 
            display: flex; 
            height: 75vh; 
            background-color: #fff; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); 
        }
        .sim-list { 
            width: 30%; 
            background-color: #f8f9fa; 
            overflow-y: auto; 
            border-right: 1px solid #dee2e6; 
        }
        .sim-list-header { 
            padding: 15px; 
            background-color: #e9ecef; 
            border-bottom: 1px solid #dee2e6; 
            position: sticky; 
            top: 0; 
            z-index: 10; 
        }
        .sim-item { 
            padding: 15px; 
            cursor: pointer; 
            border-bottom: 1px solid #eee; 
            transition: background-color 0.2s; 
            position: relative; 
        }
        .sim-item:hover { background-color: #e9ecef; }
        .sim-item.active { background-color: #3498db; color: white; }
        .sim-item .badge { 
            transition: all 0.3s ease; 
        }
        .sim-item .badge.new { 
            animation: pulse 1.5s infinite; 
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .chat-area { 
            width: 70%; 
            display: flex; 
            flex-direction: column; 
        }
        .chat-header { 
            padding: 15px; 
            background-color: #3498db; 
            color: white; 
            border-bottom: 1px solid #2980b9; 
            position: relative; 
        }
        .chat-header .clear-session { 
            position: absolute; 
            right: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            font-size: 0.9rem; 
            color: #ecf0f1; 
            cursor: pointer; 
        }
        .chat-header .clear-session:hover { color: #fff; text-decoration: underline; }
        .chat-messages { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px; 
            background-color: #f1f3f5; 
        }
        .message { 
            margin-bottom: 15px; 
            padding: 10px 15px; 
            border-radius: 15px; 
            max-width: 70%; 
            position: relative; 
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); 
        }
        .message.admin { 
            background-color: #e9ecef; 
            color: black; 
            margin-right: auto; 
            border-top-left-radius: 0; 
        }
        .message.user { 
            background-color: #3498db; 
            color: white; 
            margin-left: auto; 
            border-top-right-radius: 0; 
        }
        .message .time { 
            font-size: 0.75rem; 
            color: #adb5bd; 
            margin-top: 5px; 
        }
        .message.user .time { color: #d1e7ff; }
        .chat-input { 
            padding: 15px; 
            border-top: 1px solid #dee2e6; 
            background-color: #fff; 
        }
        .chat-input .form-control { 
            border-radius: 20px; 
            border: 1px solid #ced4da; 
        }
        .chat-input .btn { 
            border-radius: 20px; 
            margin-left: 10px; 
            transition: background-color 0.3s; 
        }
        .chat-input .btn:disabled { 
            background-color: #ced4da; 
            cursor: not-allowed; 
        }
        @media (max-width: 768px) {
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: relative; 
                top: 0; 
            }
            .content { 
                margin-left: 0; 
                padding: 15px; 
            }
            .chat-container { 
                flex-direction: column; 
                height: auto; 
                min-height: 60vh; 
            }
            .sim-list { 
                width: 100%; 
                max-height: 30vh; 
            }
            .chat-area { 
                width: 100%; 
                min-height: 50vh; 
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Sistem Manajemen SIM</a>
            <div class="user-info">
                <span class="navbar-text me-3">Selamat datang, {{ Auth::user()->email }}</span>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <h6 class="text-center mb-4">Menu</h6>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('chat.index') }}" class="active">Chat</a>
    </div>

    <div class="content">
        <h2>Chat dengan Pemegang SIM</h2>
        <div class="chat-container">
            <div class="sim-list">
                <div class="sim-list-header">
                    <h5 class="mb-0">Pemegang SIM</h5>
                </div>
                @forelse ($sims as $sim)
                    <div class="sim-item {{ session('current_sim') == $sim->nomor_sim ? 'active' : '' }}" data-sim="{{ $sim->nomor_sim }}" onclick="selectSim('{{ $sim->nomor_sim }}')">
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
                        <div class="input-group">
                            <input type="text" class="form-control" id="chatInput" placeholder="Ketik pesan..." disabled>
                            <button type="submit" class="btn btn-primary" disabled>Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            document.getElementById('chatHeaderTitle').innerHTML = `Chat dengan ${simNumber} <span class="clear-session" onclick="clearSimSession()" id="clearSessionBtn">Hapus Sesi</span>`;
            document.getElementById('clearSessionBtn').style.display = 'inline';
            loadMessages();
        }

        function clearSimSession() {
            currentSimNumber = null;
            sessionStorage.removeItem('current_sim');
            document.querySelectorAll('.sim-item').forEach(item => item.classList.remove('active'));
            document.getElementById('chatInput').disabled = true;
            document.querySelector('#chatForm button').disabled = true;
            document.getElementById('chatHeaderTitle').innerHTML = 'Pilih Pemegang SIM untuk memulai chat';
            document.getElementById('clearSessionBtn').style.display = 'none';
            document.getElementById('chatMessages').innerHTML = '<div class="text-muted text-center p-3">Pilih Pemegang SIM untuk memulai chat.</div>';
        }

        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('chatInput').value.trim();
            if (message && currentSimNumber) {
                fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message, sim_number: currentSimNumber })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('chatInput').value = '';
                        loadMessages(); // Reload pesan untuk memastikan urutan benar
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
            if (!currentSimNumber) return;
            fetch(`{{ route("chat.messages") }}?sim_number=${currentSimNumber}`)
                .then(response => response.json())
                .then(data => {
                    const chatMessages = document.getElementById('chatMessages');
                    const newMessages = data.map(msg => `
                        <div class="message ${msg.sender_type === 'admin' ? 'user' : 'admin'}">
                            <strong>${msg.sender_type === 'admin' ? 'Admin' : 'Pemegang SIM'}:</strong> ${e(msg.message)}
                            <div class="time">${msg.created_at}</div>
                        </div>
                    `).join('');
                    chatMessages.innerHTML = newMessages;
                    if (data.length > lastMessageCount) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        lastMessageCount = data.length;
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        // Fungsi untuk escaping HTML agar aman dari XSS
        function e(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        setInterval(loadMessages, 3000);
        const savedSim = sessionStorage.getItem('current_sim');
        if (savedSim) selectSim(savedSim);
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>