@extends('layouts.app')

@section('title', 'Detail SIM')

@section('css')
    <link href="{{ asset('css/sim.css') }}" rel="stylesheet">
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
        <a href="{{ route('chat.index') }}"><i class="fas fa-comments me-2"></i>Chat</a>
    </div>

    <div class="content">
        <h2 class="animate__animated animate__fadeIn text-shadow"><i class="fas fa-info-circle me-2"></i> Detail SIM</h2>
        <div class="card shadow-lg border-0 card-hover animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-id-card-alt me-2"></i> Nomor SIM</div>
                    <div class="col-8">{{ $sim->nomor_sim }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-user me-2"></i> Nama Lengkap</div>
                    <div class="col-8">{{ $sim->nama }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-id-card me-2"></i> Nomor KTP</div>
                    <div class="col-8">{{ $sim->nomor_ktp }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-map-marker-alt me-2"></i> Tempat Lahir</div>
                    <div class="col-8">{{ $sim->tempat_lahir }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-calendar-alt me-2"></i> Tanggal Lahir</div>
                    <div class="col-8">{{ $sim->tanggal_lahir ? \Carbon\Carbon::parse($sim->tanggal_lahir)->format('d-m-Y') : 'Tidak tersedia' }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-venus-mars me-2"></i> Jenis Kelamin</div>
                    <div class="col-8">{{ $sim->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-driving me-2"></i> Jenis SIM</div>
                    <div class="col-8">{{ $sim->jenis_sim }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-home me-2"></i> Alamat</div>
                    <div class="col-8">{{ $sim->alamat }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-briefcase me-2"></i> Pekerjaan</div>
                    <div class="col-8">{{ $sim->pekerjaan ?? 'Tidak tersedia' }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-calendar-check me-2"></i> Tanggal Penerbitan</div>
                    <div class="col-8">{{ $sim->tanggal_penerbitan ? \Carbon\Carbon::parse($sim->tanggal_penerbitan)->format('d-m-Y') : 'Tidak tersedia' }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-calendar-times me-2"></i> Masa Berlaku</div>
                    <div class="col-8">{{ $sim->masa_berlaku ? \Carbon\Carbon::parse($sim->masa_berlaku)->format('d-m-Y') : 'Tidak tersedia' }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-4 fw-bold text-muted"><i class="fas fa-info me-2"></i> Status</div>
                    <div class="col-8">
                        <span class="badge {{ $sim->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">{{ $sim->status }}</span>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2">
                    <a href="{{ route('sim.edit', $sim->sim_id) }}" class="btn btn-primary w-100"><i class="fas fa-edit me-2"></i> Edit</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/sim.js') }}"></script>
@endsection