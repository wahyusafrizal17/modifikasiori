<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::table('packing_items', function (Blueprint $table) {
            $table->foreignId('packing_detail_id')->nullable()->after('packing_id')->constrained('packing_details')->cascadeOnDelete();
        });

        // Migrate existing: each packing becomes one detail; link items to that detail
        $packings = DB::table('packings')->get();
        foreach ($packings as $p) {
            $detailId = DB::table('packing_details')->insertGetId([
                'packing_id' => $p->id,
                'product_id' => $p->product_id,
                'quantity' => $p->quantity,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ]);
            DB::table('packing_items')->where('packing_id', $p->id)->update(['packing_detail_id' => $detailId]);
        }

        Schema::table('packings', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity']);
        });
    }

    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('tanggal')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(0)->after('product_id');
        });

        // Rollback: take first detail per packing as the "main" product
        $packings = DB::table('packings')->get();
        foreach ($packings as $p) {
            $first = DB::table('packing_details')->where('packing_id', $p->id)->orderBy('id')->first();
            if ($first) {
                DB::table('packings')->where('id', $p->id)->update([
                    'product_id' => $first->product_id,
                    'quantity' => $first->quantity,
                ]);
            }
        }
        Schema::table('packings', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });

        Schema::table('packing_items', function (Blueprint $table) {
            $table->dropForeign(['packing_detail_id']);
            $table->dropColumn('packing_detail_id');
        });

        Schema::dropIfExists('packing_details');
    }
};
