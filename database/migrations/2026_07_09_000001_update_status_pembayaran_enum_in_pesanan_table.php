<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan nilai DP ke ENUM terlebih dahulu
        DB::statement("
            ALTER TABLE pesanan
            MODIFY status_pembayaran
            ENUM('belum_bayar','sebagian','DP','lunas','dikembalikan')
            NOT NULL DEFAULT 'belum_bayar'
        ");

        // Ubah data lama menjadi DP
        DB::table('pesanan')
            ->where('status_pembayaran', 'sebagian')
            ->update([
                'status_pembayaran' => 'DP'
            ]);

        // Hapus nilai 'sebagian' dari ENUM
        DB::statement("
            ALTER TABLE pesanan
            MODIFY status_pembayaran
            ENUM('belum_bayar','DP','lunas','dikembalikan')
            NOT NULL DEFAULT 'belum_bayar'
        ");
    }

    public function down(): void
    {
        // Tambahkan kembali nilai 'sebagian'
        DB::statement("
            ALTER TABLE pesanan
            MODIFY status_pembayaran
            ENUM('belum_bayar','sebagian','DP','lunas','dikembalikan')
            NOT NULL DEFAULT 'belum_bayar'
        ");

        // Kembalikan data DP menjadi sebagian
        DB::table('pesanan')
            ->where('status_pembayaran', 'DP')
            ->update([
                'status_pembayaran' => 'sebagian'
            ]);

        // Hapus nilai DP
        DB::statement("
            ALTER TABLE pesanan
            MODIFY status_pembayaran
            ENUM('belum_bayar','sebagian','lunas','dikembalikan')
            NOT NULL DEFAULT 'belum_bayar'
        ");
    }
};