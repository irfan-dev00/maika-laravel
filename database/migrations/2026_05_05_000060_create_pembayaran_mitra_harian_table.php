<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranMitraHarianTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_mitra_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('laporan_penjualan_mitra_id')->constrained('laporan_penjualan_mitra')->cascadeOnDelete();
            $table->decimal('jumlah_bayar', 14, 2)->default(0);
            $table->string('metode')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'confirmed'])->default('confirmed');
            $table->timestamps();

            $table->unique(['laporan_penjualan_mitra_id'], 'pembayaran_laporan_unq');
            $table->index(['tanggal', 'mitra_id'], 'pembayaran_tanggal_mitra_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_mitra_harian');
    }
}

