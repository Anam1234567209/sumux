<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pesanan', 'status_pesanan')) {
            DB::statement("ALTER TABLE `pesanan` MODIFY COLUMN `status_pesanan` VARCHAR(50) NOT NULL DEFAULT 'pending'");
        }

        if (Schema::hasColumn('pesanan', 'status_packing')) {
            DB::statement("ALTER TABLE `pesanan` MODIFY COLUMN `status_packing` VARCHAR(50) NOT NULL DEFAULT 'belum'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanan', 'status_pesanan')) {
            DB::statement("ALTER TABLE `pesanan` MODIFY COLUMN `status_pesanan` ENUM('pending','produksi','finishing','selesai','dikirim','dibatalkan') NOT NULL DEFAULT 'pending'");
        }

        if (Schema::hasColumn('pesanan', 'status_packing')) {
            DB::statement("ALTER TABLE `pesanan` MODIFY COLUMN `status_packing` ENUM('belum','proses','selesai') NOT NULL DEFAULT 'belum'");
        }
    }
};
