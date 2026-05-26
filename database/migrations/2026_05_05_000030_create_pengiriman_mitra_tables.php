<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengirimanMitraTables extends Migration
{
    public function up()
    {
        Schema::create('pengiriman_mitra', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'mitra_id'], 'pengiriman_tanggal_mitra_idx');
        });

        Schema::create('detail_pengiriman_mitra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_mitra_id')->constrained('pengiriman_mitra')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_titip')->default(0);
            $table->decimal('harga_jual', 14, 2);
            $table->decimal('margin_per_unit', 14, 2)->nullable();
            $table->timestamps();

            $table->unique(['pengiriman_mitra_id', 'produk_id'], 'pengiriman_produk_unq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_pengiriman_mitra');
        Schema::dropIfExists('pengiriman_mitra');
    }
}

