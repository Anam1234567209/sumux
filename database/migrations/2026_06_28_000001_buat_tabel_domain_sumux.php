<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan')->unique();
            $table->string('nama_pelanggan');
            $table->string('email')->nullable()->index();
            $table->string('nomor_telepon', 30)->nullable();
            $table->string('nomor_whatsapp', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->unique();
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_produk_id')->nullable()->constrained('kategori_produk')->nullOnDelete();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('slug')->unique();
            $table->enum('jenis_produk', ['property', 'interior', 'furniture', 'jasa'])->default('interior');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('berat_gram')->default(0);
            $table->decimal('panjang_cm', 10, 2)->nullable();
            $table->decimal('lebar_cm', 10, 2)->nullable();
            $table->decimal('tinggi_cm', 10, 2)->nullable();
            $table->string('satuan')->default('pcs');
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(['jenis_produk', 'aktif']);
        });

        Schema::create('gambar_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('judul_gambar')->nullable();
            $table->string('path_gambar');
            $table->string('teks_alternatif')->nullable();
            $table->boolean('utama')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_pesanan')->unique();
            $table->date('tanggal_pesanan');
            $table->date('tanggal_target_selesai')->nullable();
            $table->enum('status_pesanan', ['pending', 'produksi', 'finishing', 'selesai', 'dikirim', 'dibatalkan'])->default('pending');
            $table->enum('status_pembayaran', ['belum_bayar', 'DP', 'lunas', 'dikembalikan'])->default('belum_bayar');
            $table->enum('status_packing', ['belum', 'proses', 'selesai'])->default('belum');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('biaya_lain', 15, 2)->default(0);
            $table->decimal('biaya_ongkir', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->text('alamat_pengiriman')->nullable();
            $table->string('provinsi_pengiriman')->nullable();
            $table->string('kota_pengiriman')->nullable();
            $table->string('kecamatan_pengiriman')->nullable();
            $table->string('kelurahan_pengiriman')->nullable();
            $table->string('kode_pos_pengiriman', 10)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(['tanggal_pesanan', 'status_pesanan']);
            $table->index(['status_pembayaran', 'status_packing']);
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->string('nama_item');
            $table->text('deskripsi_item')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->default('pcs');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->integer('berat_gram')->default(0);
            $table->decimal('panjang_cm', 10, 2)->nullable();
            $table->decimal('lebar_cm', 10, 2)->nullable();
            $table->decimal('tinggi_cm', 10, 2)->nullable();
            $table->string('path_preview')->nullable();
            $table->text('catatan_produksi')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_pembayaran');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['transfer', 'tunai', 'kartu', 'qris', 'lainnya'])->default('transfer');
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->string('path_bukti_bayar')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(['tanggal_pembayaran', 'status_verifikasi']);
        });

        Schema::create('kurir', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kurir')->unique();
            $table->string('nama_kurir');
            $table->string('nama_layanan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('kurir_id')->nullable()->constrained('kurir')->nullOnDelete();
            $table->string('nomor_resi')->nullable()->index();
            $table->string('kode_kurir')->nullable();
            $table->string('nama_kurir')->nullable();
            $table->string('layanan_kurir')->nullable();
            $table->decimal('biaya_ongkir', 15, 2)->default(0);
            $table->integer('berat_gram')->default(0);
            $table->string('estimasi_sampai')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_terima')->nullable();
            $table->enum('status_pengiriman', ['menunggu', 'diproses', 'dikirim', 'diterima', 'gagal'])->default('menunggu');
            $table->text('alamat_asal')->nullable();
            $table->string('id_wilayah_asal')->nullable();
            $table->text('alamat_tujuan')->nullable();
            $table->string('id_wilayah_tujuan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(['tanggal_kirim', 'status_pengiriman']);
        });

        Schema::create('riwayat_status_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah');
            $table->text('keterangan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
        });

        Schema::create('pengecekan_ongkir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('id_wilayah_asal');
            $table->string('label_wilayah_asal')->nullable();
            $table->string('id_wilayah_tujuan');
            $table->string('label_wilayah_tujuan')->nullable();
            $table->integer('berat_gram');
            $table->string('kode_kurir');
            $table->string('nama_kurir')->nullable();
            $table->string('layanan_kurir')->nullable();
            $table->decimal('biaya_ongkir', 15, 2)->nullable();
            $table->string('estimasi_sampai')->nullable();
            $table->json('respon_api')->nullable();
            $table->timestamp('dibuat_pada')->nullable();

            $table->index(['kode_kurir', 'dibuat_pada']);
        });

        Schema::create('pengaturan_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->string('grup_pengaturan')->default('umum');
            $table->string('kunci_pengaturan');
            $table->text('nilai_pengaturan')->nullable();
            $table->string('tipe_nilai')->default('teks');
            $table->boolean('publik')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->unique(['grup_pengaturan', 'kunci_pengaturan']);
        });

        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe_notifikasi')->default('info');
            $table->string('tautan')->nullable();
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(['pengguna_id', 'sudah_dibaca']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('pengaturan_aplikasi');
        Schema::dropIfExists('pengecekan_ongkir');
        Schema::dropIfExists('riwayat_status_pesanan');
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('kurir');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('gambar_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori_produk');
        Schema::dropIfExists('pelanggan');
    }
};
