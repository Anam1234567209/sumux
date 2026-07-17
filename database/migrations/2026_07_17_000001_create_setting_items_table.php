<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_items', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_items');
    }
};
