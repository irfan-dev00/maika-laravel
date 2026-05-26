<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduksiHarianTables extends Migration
{
    public function up()
    {
        Schema::create('produksi_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_produksi_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_harian_id')->constrained('produksi_harian')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->unsignedInteger('stok_awal')->default(0);
            $table->unsignedInteger('jumlah_produksi')->default(0);
            $table->unsignedInteger('stok_layak_jual_kembali')->default(0);
            $table->unsignedInteger('stok_siap_jual')->default(0);
            $table->timestamps();

            $table->unique(['produksi_harian_id', 'produk_id'], 'produksi_produk_unq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_produksi_harian');
        Schema::dropIfExists('produksi_harian');
    }
}

