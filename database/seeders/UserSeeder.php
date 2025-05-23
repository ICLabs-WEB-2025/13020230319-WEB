<?php

  namespace Database\Seeders;

  use Illuminate\Database\Seeder;
  use App\Models\User;

  class UserSeeder extends Seeder
  {
      public function run(): void
      {
          User::firstOrCreate(
              ['email' => 'admin@example.com'],
              [
                  'password' => bcrypt('admin123'),
                  'role' => 'Admin',
                  'created_at' => now(),
                  'updated_at' => now(),
              ]
          );
      }
  }