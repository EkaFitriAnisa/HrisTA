<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_aset', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 100);
            $table->enum('managed_role', [
                'HRD',
                'HOD',
                'MIS'
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_aset');
    }
};