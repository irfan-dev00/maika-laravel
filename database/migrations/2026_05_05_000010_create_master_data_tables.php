<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDataTables extends Migration
{
    public function up()
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mitra')->unique();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama');
            $table->string('satuan')->nullable();
            $table->decimal('harga_modal_per_unit', 14, 2)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('kalender_operasional', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->enum('status', ['operasional', 'libur']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('harga_produk_mitra_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->decimal('harga_jual', 14, 2);
            $table->decimal('margin_per_unit', 14, 2)->nullable();
            $table->timestamps();

            $table->unique(['mitra_id', 'produk_id', 'tahun', 'bulan'], 'harga_mitra_produk_bulan_unq');
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('role')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id'], 'role_user_unq');
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('harga_produk_mitra_bulanan');
        Schema::dropIfExists('kalender_operasional');
        Schema::dropIfExists('role');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('mitra');
    }
}

