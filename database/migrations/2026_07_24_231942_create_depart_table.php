<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depart', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code_depart', 50)->unique();
            $table->string('nama', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depart');
    }
};