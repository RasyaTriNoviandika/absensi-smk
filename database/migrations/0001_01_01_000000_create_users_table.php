<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10)->unique()->nullable(); // Untuk siswa
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'student'])->default('student');
            $table->string('class')->nullable(); // Contoh: "10 DKV 1"
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('face_descriptor')->nullable(); // JSON array face encoding
            $table->string('profile_photo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
