<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nim')->unique();
            $table->string('alamat');
            $table->string('no_telepon');
            $table->year('tahun_masuk');
            $table->integer('semester_aktif')->default(1);
            $table->decimal('total_tunggakan', 12, 2)->default(0);
            $table->enum('status_mahasiswa', ['aktif', 'cuti', 'do'])->default('aktif');
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa');
            $table->timestamp('email_verified_at')->nullable();
            $table->longText('foto')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
