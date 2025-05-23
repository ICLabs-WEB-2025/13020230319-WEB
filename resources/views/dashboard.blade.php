@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
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
        .table-container { 
            background-color: #fff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); 
        }
        @media (max-width: 768px) {
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: relative; 
                top: 0; 
            }
            .content { margin-left: 0; padding: 15px; }
        }
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
        <a href="{{ route('dashboard') }}" class="active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="{{ route('sim.create') }}"><i class="fas fa-plus-circle me-2"></i>Tambah SIM</a>
        <a href="{{ route('chat.index') }}"><i class="fas fa-comments me-2"></i>Chat</a>
    </div>

    <div class="content">
        <h2 class="animate__animated animate__fadeIn">Dashboard Admin</h2>
        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="filter_type" class="form-select">
                                <option value="penerbitan" {{ $filterType == 'penerbitan' ? 'selected' : '' }}>Tanggal Penerbitan</option>
                                <option value="berlaku" {{ $filterType == 'berlaku' ? 'selected' : '' }}>Masa Berlaku</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="filter" class="form-select">
                                <option value="">Semua</option>
                                <option value="baru" {{ $filter == 'baru' ? 'selected' : '' }}>Baru</option>
                                <option value="lama" {{ $filter == 'lama' ? 'selected' : '' }}>Lama</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex">
                        <input type="text" name="query" class="form-control me-2" placeholder="Cari SIM..." value="{{ $query ?? '' }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nomor SIM</th>
                        <th>Nama</th>
                        <th>Jenis SIM</th>
                        <th>Tanggal Penerbitan</th>
                        <th>Masa Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sims as $sim)
                        <tr>
                            <td>{{ $sim->nomor_sim }}</td>
                            <td>{{ $sim->nama }}</td>
                            <td>{{ $sim->jenis_sim }}</td>
                            <td>{{ $sim->tanggal_penerbitan ? \Carbon\Carbon::parse($sim->tanggal_penerbitan)->format('d-m-Y') : 'Tidak tersedia' }}</td>
                            <td>{{ $sim->masa_berlaku ? \Carbon\Carbon::parse($sim->masa_berlaku)->format('d-m-Y') : 'Tidak tersedia' }}</td>
                            <td>{{ $sim->status }}</td>
                            <td>
                                <a href="{{ route('sim.view', $sim->sim_id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('sim.edit', $sim->sim_id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('sim.delete', $sim->sim_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus SIM ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data SIM.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sims->appends(request()->query())->links() }}
        </div>
        <div class="mt-4">
            <a href="{{ route('sims.export.pdf') }}" class="btn btn-success">Export PDF</a>
            <a href="{{ route('sims.export.csv') }}" class="btn btn-success">Export CSV</a>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        document.getElementById('logout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Logout form submitted');
            fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => {
                console.log('Logout response status:', response.status);
                if (response.ok) {
                    window.location.href = '{{ route('admin.login') }}';
                } else {
                    alert('Gagal logout. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                alert('Terjadi kesalahan saat logout.');
            });
        });
    </script>
@endsection