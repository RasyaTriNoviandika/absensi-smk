<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->enum('check_in_status', ['hadir', 'terlambat'])->nullable();
            $table->string('check_in_photo')->nullable();
            $table->time('check_out')->nullable();
            $table->string('check_out_photo')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'alpha'])->default('alpha');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Satu siswa hanya bisa absen sekali per hari
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};