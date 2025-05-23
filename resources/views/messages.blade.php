<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Sistem Manajemen SIM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            width: 250px;
        }
        .sidebar .nav-link {
            color: white !important;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .navbar { background: #007bff; padding: 10px; color: white; }
        .navbar a { color: white; text-decoration: none; margin: 0 10px; }
        .chat-container { max-width: 800px; margin: 0 auto; }
        .message-list { max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: white; }
        .message-item { margin-bottom: 10px; padding: 10px; border-radius: 5px; }
        .message-item.admin { background: #e9ecef; }
        .message-item.user { background: #d1e7dd; }
        .message-form { margin-top: 20px; }
        .notifikasi { background: #ffeb3b; padding: 10px; margin: 10px 0; }
        @media (max-width: 768px) {
            .sidebar {
                height: auto;
                position: relative;
                width: 100%;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4 class="text-white">SISTEM MANAJEMEN SIM</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('sim.create') }}">Tambah SIM</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('sim.search') }}">Cari Data SIM</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('chat.index') }}">Chat</a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div>Sistem Pengelolaan SIM</div>
        <div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- Content Area -->
    <div class="content">
        <div class="chat-container">
            <h1>Chat Admin - Pengguna</h1>

            @if (session('success'))
                <div class="notifikasi">{{ session('success') }}</div>
            @endif

            <div class="message-list">
                @forelse ($messages as $message)
                    <div class="message-item {{ $message->pengirim === 'Admin' ? 'admin' : 'user' }}">
                        <strong>{{ $message->pengirim }} (SIM ID: {{ $message->sim_id }})</strong><br>
                        {{ $message->pesan }}<br>
                        <small>{{ $message->timestamp->format('d-m-Y H:i') }}</small>
                        @if (!$message->is_read && $message->pengirim !== 'Admin')
                            <a href="{{ route('chat.read', $message->message_id) }}" class="btn btn-sm btn-primary mt-2">Tandai Dibaca</a>
                        @endif
                    </div>
                @empty
                    <p>Tidak ada pesan.</p>
                @endforelse
            </div>

            <div class="message-form">
                <h3>Kirim Pesan Baru</h3>
                <form action="{{ route('chat.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="sim_id" class="form-label">Pilih SIM</label>
                        <select name="sim_id" id="sim_id" class="form-control" required>
                            @foreach ($sims as $sim)
                                <option value="{{ $sim->sim_id }}">{{ $sim->nomor_sim }} - {{ $sim->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pesan" class="form-label">Pesan</label>
                        <textarea name="pesan" id="pesan" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <p class="mb-0">© {{ date('Y') }} Sistem Manajemen SIM. Hak Cipta Dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>