<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_type'); // 'admin' atau 'user'
            $table->string('sender_id'); // ID admin atau nomor SIM
            $table->string('receiver_id'); // ID admin (default 1) atau nomor SIM
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Indeks untuk performa query
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('sender_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};