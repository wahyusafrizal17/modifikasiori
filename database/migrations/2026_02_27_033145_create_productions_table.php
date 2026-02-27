<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produksi')->unique();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['proses', 'qc', 'selesai', 'gagal'])->default('proses');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
