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
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran');
            $table->string('semester');
            $table->string('tahun_ajaran');
            $table->decimal('jumlah_tagihan', 12, 2);
            $table->decimal('jumlah_terbayar', 12, 2)->default(0);
            $table->decimal('sisa_tagihan', 12, 2);
            $table->integer('cicilan_ke')->nullable();
            $table->integer('total_cicilan')->nullable();
            $table->enum('status', ['belum_bayar', 'cicilan', 'lunas'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
