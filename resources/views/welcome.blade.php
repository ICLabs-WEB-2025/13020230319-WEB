@extends('layouts.app')

@section('title', 'Sistem Manajemen SIM')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .welcome-container { max-width: 600px; margin: 50px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .welcome-container h1 { text-align: center; margin-bottom: 20px; }
        .search-container { max-width: 500px; margin: 20px auto; padding: 20px; background-color: #f8f9fa; border-radius: 10px; }
        .form-control { border-radius: 20px; }
        .btn { border-radius: 20px; }
        .admin-login-btn { margin-bottom: 20px; }
    </style>
@endsection

@section('content')
    <div class="welcome-container">
        <h1 class="text-center">Sistem Manajemen SIM</h1>
        <div class="text-center admin-login-btn">
            <a href="{{ route('admin.login') }}" class="btn btn-primary">Login Admin</a>
        </div>
        <div class="search-container">
            <h3 class="text-center mb-4">Cari Data SIM</h3>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('sim.public-search') }}" id="searchForm">
                @csrf
                <div class="mb-3">
                    <label for="sim_number" class="form-label">Nomor SIM</label>
                    <input type="text" class="form-control" id="sim_number" name="sim_number" required>
                </div>
                <div class="mb-3">
                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cari</button>
            </form>
            @if ($sim)
                <div class="mt-4">
                    <h4>Data SIM Ditemukan:</h4>
                    <p><strong>Nomor SIM:</strong> {{ $sim->nomor_sim }}</p>
                    <p><strong>Nama:</strong> {{ $sim->nama }}</p>
                    <a href="{{ route('chat.user') }}" class="btn btn-success mt-3">Chat dengan Admin</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            console.log('Form submitted with CSRF token:', document.querySelector('meta[name="csrf-token"]').content);
        });
    </script>
@endsection