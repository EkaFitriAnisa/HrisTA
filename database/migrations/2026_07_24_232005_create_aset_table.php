<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kategori_id');
            $table->unsignedInteger('depart_id');
            $table->string('nama', 150);
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');
            $table->string('asset_no', 100)->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('username', 150)->nullable();
            $table->string('platform', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_plat', 50)->nullable();
            $table->string('cc_last4', 10)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->foreign('kategori_id')
                ->references('id')
                ->on('kategori_aset')
                ->restrictOnDelete();
            $table->foreign('depart_id')
                ->references('id')
                ->on('depart')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};