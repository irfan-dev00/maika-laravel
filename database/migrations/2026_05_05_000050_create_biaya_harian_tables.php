<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiayaHarianTables extends Migration
{
    public function up()
    {
        Schema::create('biaya_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->text('catatan')->nullable();
            $table->decimal('total_biaya', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('detail_biaya_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_harian_id')->constrained('biaya_harian')->cascadeOnDelete();
            $table->string('nama_item');
            $table->decimal('qty', 14, 4)->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('harga_satuan', 14, 2)->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_biaya_harian');
        Schema::dropIfExists('biaya_harian');
    }
}

