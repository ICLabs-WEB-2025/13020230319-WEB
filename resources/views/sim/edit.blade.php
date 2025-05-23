@extends('layouts.app')

@section('title', 'Edit SIM')

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
        <h2 class="animate__animated animate__fadeIn text-shadow"><i class="fas fa-edit me-2"></i> Edit SIM</h2>
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInUp" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-lg border-0 card-hover animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <form action="{{ route('sim.update', $sim->sim_id) }}" method="POST" id="editSimForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label"><i class="fas fa-user me-2"></i> Nama Lengkap</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $sim->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomor_ktp" class="form-label"><i class="fas fa-id-card me-2"></i> Nomor KTP</label>
                            <input type="text" class="form-control @error('nomor_ktp') is-invalid @enderror" id="nomor_ktp" name="nomor_ktp" value="{{ old('nomor_ktp', $sim->nomor_ktp) }}" required>
                            @error('nomor_ktp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tempat_lahir" class="form-label"><i class="fas fa-map-marker-alt me-2"></i> Tempat Lahir</label>
                            <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $sim->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label"><i class="fas fa-calendar-alt me-2"></i> Tanggal Lahir</label>
                            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $sim->tanggal_lahir ? \Carbon\Carbon::parse($sim->tanggal_lahir)->format('Y-m-d') : '') }}" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_kelamin" class="form-label"><i class="fas fa-venus-mars me-2"></i> Jenis Kelamin</label>
                            <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin', $sim->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $sim->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_sim" class="form-label"><i class="fas fa-driving me-2"></i> Jenis SIM</label>
                            <select class="form-select @error('jenis_sim') is-invalid @enderror" id="jenis_sim" name="jenis_sim" required>
                                <option value="" disabled>Pilih Jenis SIM</option>
                                <option value="A" {{ old('jenis_sim', $sim->jenis_sim) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('jenis_sim', $sim->jenis_sim) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ old('jenis_sim', $sim->jenis_sim) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ old('jenis_sim', $sim->jenis_sim) == 'D' ? 'selected' : '' }}>D</option>
                            </select>
                            @error('jenis_sim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label"><i class="fas fa-home me-2"></i> Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $sim->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pekerjaan" class="form-label"><i class="fas fa-briefcase me-2"></i> Pekerjaan (Opsional)</label>
                        <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $sim->pekerjaan) }}">
                        @error('pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 "><i class="fas fa-save me-2"></i> Update SIM</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/sim.js') }}"></script>
@endsection