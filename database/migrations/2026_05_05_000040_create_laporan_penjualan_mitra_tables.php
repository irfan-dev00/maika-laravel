<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanPenjualanMitraTables extends Migration
{
    public function up()
    {
        Schema::create('laporan_penjualan_mitra', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['mitra_id', 'tanggal'], 'laporan_mitra_tanggal_unq');
            $table->index(['tanggal', 'mitra_id'], 'laporan_tanggal_mitra_idx');
        });

        Schema::create('detail_laporan_penjualan_mitra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_penjualan_mitra_id');
            $table->unsignedBigInteger('produk_id');

            $table->unsignedInteger('jumlah_titip')->default(0);
            $table->unsignedInteger('sisa_barang')->default(0);
            $table->unsignedInteger('stok_layak_jual_kembali')->default(0);

            $table->unsignedInteger('jumlah_terjual')->default(0);
            $table->unsignedInteger('stok_tidak_layak_jual')->default(0);

            $table->decimal('harga_jual', 14, 2);
            $table->decimal('margin_per_unit', 14, 2)->nullable();
            $table->decimal('total_penjualan', 14, 2)->default(0);
            $table->decimal('total_margin', 14, 2)->default(0);

            $table->timestamps();

            $table->unique(['laporan_penjualan_mitra_id', 'produk_id'], 'laporan_produk_unq');
            $table->foreign('laporan_penjualan_mitra_id', 'dlpm_lpm_fk')->references('id')->on('laporan_penjualan_mitra')->onDelete('cascade');
            $table->foreign('produk_id', 'dlpm_produk_fk')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_laporan_penjualan_mitra');
        Schema::dropIfExists('laporan_penjualan_mitra');
    }
}
