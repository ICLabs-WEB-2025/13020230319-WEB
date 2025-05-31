@extends('layouts.app')

@section('title', 'Cari SIM')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endsection

@section('content')
    <div class="welcome-hero">
        <h1><i class="fas fa-address-card me-2"></i> Cari Detail SIM Anda</h1>
        <p>Masukkan nomor SIM dan tanggal lahir Anda untuk melihat informasi detail.</p>
    </div>

    <div class="search-form">
        <form action="{{ route('sim.public-search') }}" method="POST">
            @csrf
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                <input type="text" name="nomor_sim" class="form-control @error('nomor_sim') is-invalid @enderror" placeholder="Masukkan Nomor SIM" required>
                @error('nomor_sim')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                @error('tanggal_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="input-group">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cari</button>
            </div>
        </form>
    </div>

    @if (isset($sim))
        <div class="sim-card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Detail SIM
            </div>
            <div class="card-body">
                <div class="sim-detail">
                    <i class="fas fa-id-card"></i>
                    <div>
                        <strong>Nomor SIM</strong>
                        <span>{{ $sim->nomor_sim }}</span>
                    </div>
                </div>
                <div class="sim-detail">
                    <i class="fas fa-car"></i>
                    <div>
                        <strong>Jenis SIM</strong>
                        <span>{{ $sim->jenis_sim }}</span>
                    </div>
                </div>
                <div class="sim-detail">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <strong>Masa Berlaku</strong>
                        <span>{{ \Carbon\Carbon::parse($sim->masa_berlaku)->format('d-m-Y') }}</span>
                    </div>
                </div>
                <a href="{{ route('chat.user') }}" class="chat-link">
                    <i class="fas fa-comments"></i> Chat dengan Admin
                </a>
            </div>
        </div>
    @endif
@endsection