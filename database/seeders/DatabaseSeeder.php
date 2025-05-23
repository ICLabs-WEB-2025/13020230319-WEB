<?php

  namespace Database\Seeders;

  use Illuminate\Database\Seeder;
  use App\Models\Sim;
  use App\Models\Message;

  class DatabaseSeeder extends Seeder
  {
      public function run(): void
      {
          $this->call(UserSeeder::class);

          Sim::create([
              'user_id' => 1,
              'nomor_sim' => 'SIM-2025-000001',
              'nama' => 'Budi Santoso',
              'tempat_lahir' => 'Jakarta',
              'tanggal_lahir' => '1990-05-15',
              'jenis_kelamin' => 'L',
              'alamat' => 'Jl. Sudirman No. 1',
              'pekerjaan' => 'Pegawai Negeri',
              'jenis_sim' => 'A',
              'nomor_ktp' => '1234567890123456',
              'tanggal_penerbitan' => '2025-05-01',
              'masa_berlaku' => '2030-05-01',
              'status' => 'Aktif',
              'created_at' => now(),
              'updated_at' => now(),
          ]);

          Message::create([
              'sim_id' => 1,
              'user_id' => 1,
              'pesan' => 'Halo, status SIM Anda aktif.',
              'pengirim' => 'Admin',
              'timestamp' => now(),
              'is_read' => false,
          ]);
      }
  }