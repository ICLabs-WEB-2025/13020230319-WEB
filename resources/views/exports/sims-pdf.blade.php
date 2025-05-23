<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Laporan: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nomor SIM</th>
                <th>Nama</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Pekerjaan</th>
                <th>Jenis SIM</th>
                <th>Nomor KTP</th>
                <th>Tanggal Penerbitan</th>
                <th>Masa Berlaku</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sims as $sim)
                <tr>
                    <td>{{ $sim->nomor_sim }}</td>
                    <td>{{ $sim->nama ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->tempat_lahir ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->tanggal_lahir ? \Carbon\Carbon::parse($sim->tanggal_lahir)->format('d-m-Y') : 'Tidak tersedia' }}</td>
                    <td>{{ $sim->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $sim->alamat ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->pekerjaan ?? 'Tidak ada' }}</td>
                    <td>{{ $sim->jenis_sim ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->nomor_ktp ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->tanggal_penerbitan ? \Carbon\Carbon::parse($sim->tanggal_penerbitan)->format('d-m-Y') : 'Tidak tersedia' }}</td>
                    <td>{{ $sim->masa_berlaku ? \Carbon\Carbon::parse($sim->masa_berlaku)->format('d-m-Y') : 'Tidak tersedia' }}</td>
                    <td>{{ $sim->status ?? 'Tidak tersedia' }}</td>
                    <td>{{ $sim->created_at ? \Carbon\Carbon::parse($sim->created_at)->format('d-m-Y H:i:s') : 'Tidak tersedia' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>