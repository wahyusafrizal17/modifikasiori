<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['bahan_siap_produksi', 'kemasan']);
            $table->foreignId('bahan_siap_produksi_id')->nullable()->constrained('bahan_siap_produksis')->nullOnDelete();
            $table->foreignId('kemasan_id')->nullable()->constrained('kemasans')->nullOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_items');
    }
};
