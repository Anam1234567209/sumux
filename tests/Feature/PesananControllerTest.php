<?php

namespace Tests\Feature;

use App\Http\Controllers\PesananController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PesananControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('bank_customer');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('pesanan');

        Schema::create('pelanggan', function ($table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('email')->nullable();
            $table->string('nomor_whatsapp')->nullable();
            $table->timestamps();
        });

        Schema::create('pesanan', function ($table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan');
            $table->unsignedBigInteger('pengguna_id')->nullable();
            $table->string('nomor_pesanan');
            $table->string('nomor_whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->date('tanggal_pesanan');
            $table->string('status_pesanan')->default('pending');
            $table->string('status_pembayaran')->nullable();
            $table->string('status_packing')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_tagihan', 15, 2)->default(0);
            $table->decimal('biaya_ongkir', 15, 2)->default(0);
            $table->text('alamat_pengiriman')->nullable();
            $table->string('provinsi_pengiriman')->nullable();
            $table->string('kota_pengiriman')->nullable();
            $table->string('kecamatan_pengiriman')->nullable();
            $table->string('kelurahan_pengiriman')->nullable();
            $table->string('kode_pos_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('bank_customer', function ($table) {
            $table->id();
            $table->string('nama_bank');
            $table->timestamps();
        });

        Schema::create('pembayaran', function ($table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('metode_pembayaran')->nullable();
            $table->string('nama_bank')->nullable();
            $table->timestamps();
        });

        Schema::create('pengiriman', function ($table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('kurir_id')->nullable();
            $table->string('nama_kurir')->nullable();
            $table->string('layanan_kurir')->nullable();
            $table->string('status_pengiriman')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('detail_pesanan', function ($table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('nama_item');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('path_preview')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });

        Schema::create('progres', function ($table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('packing', function ($table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('ekspedisi', function ($table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function test_edit_returns_order_data_as_json(): void
    {
        $pelanggan = DB::table('pelanggan')->insertGetId([
            'nama_pelanggan' => 'Budi',
            'email' => 'budi@example.com',
            'nomor_whatsapp' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pesananId = DB::table('pesanan')->insertGetId([
            'pelanggan_id' => $pelanggan,
            'nomor_pesanan' => 'SUM-20260725-0001',
            'tanggal_pesanan' => '2026-07-25',
            'status_pesanan' => 'pending',
            'status_packing' => 'draft',
            'total_tagihan' => 100000,
            'total_dibayar' => 50000,
            'sisa_tagihan' => 50000,
            'biaya_ongkir' => 15000,
            'alamat_pengiriman' => 'Jl. Merdeka 1',
            'provinsi_pengiriman' => 'Jawa Tengah',
            'kota_pengiriman' => 'Semarang',
            'kecamatan_pengiriman' => 'Candisari',
            'kelurahan_pengiriman' => 'Gisikdrono',
            'kode_pos_pengiriman' => '50244',
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        DB::table('bank_customer')->insert([
            'nama_bank' => 'BCA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembayaran')->insert([
            'pesanan_id' => $pesananId,
            'metode_pembayaran' => 'transfer',
            'nama_bank' => 'BCA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pengiriman')->insert([
            'pesanan_id' => $pesananId,
            'kurir_id' => 1,
            'nama_kurir' => 'JNE',
            'layanan_kurir' => 'REG',
            'status_pengiriman' => 'menunggu',
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        DB::table('detail_pesanan')->insert([
            'pesanan_id' => $pesananId,
            'nama_item' => 'Kursi',
            'harga_satuan' => 100000,
            'jumlah' => 1,
            'satuan' => 'unit',
            'subtotal' => 100000,
            'path_preview' => '/storage/test.jpg',
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        $controller = new PesananController();
        $response = $controller->edit($pesananId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($pesananId, $response->getData(true)['id']);
        $this->assertSame('Jawa Tengah', $response->getData(true)['provinsi']);
        $this->assertCount(1, $response->getData(true)['items']);
    }

    public function test_update_accepts_rupiah_formatted_item_prices(): void
    {
        $pelanggan = DB::table('pelanggan')->insertGetId([
            'nama_pelanggan' => 'Budi',
            'email' => 'budi@example.com',
            'nomor_whatsapp' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pesananId = DB::table('pesanan')->insertGetId([
            'pelanggan_id' => $pelanggan,
            'nomor_pesanan' => 'SUM-20260725-0002',
            'tanggal_pesanan' => '2026-07-25',
            'status_pesanan' => 'pending',
            'status_packing' => 'draft',
            'total_tagihan' => 100000,
            'total_dibayar' => 0,
            'sisa_tagihan' => 100000,
            'biaya_ongkir' => 0,
            'alamat_pengiriman' => 'Jl. Merdeka 1',
            'provinsi_pengiriman' => 'Jawa Tengah',
            'kota_pengiriman' => 'Semarang',
            'kecamatan_pengiriman' => 'Candisari',
            'kelurahan_pengiriman' => 'Gisikdrono',
            'kode_pos_pengiriman' => '50244',
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        DB::table('packing')->insert([
            ['nama' => 'Draft', 'kode' => 'DRAFT', 'aktif' => true, 'urutan' => 1],
        ]);

        $request = Request::create('/admin/pesanan/' . $pesananId, 'PUT', [
            'customer_name' => 'Budi',
            'nomor_pesanan' => 'SUM-20260725-0002',
            'tanggal_pesanan' => '2026-07-25',
            'status_pesanan' => 'pending',
            'status_packing' => 'draft',
            'total_tagihan' => '100.000',
            'total_dibayar' => '0',
            'metode_pembayaran' => null,
            'kurir_id' => null,
            'item_name' => ['Kursi'],
            'item_qty' => ['1'],
            'item_unit' => ['unit'],
            'item_price' => ['100.000'],
            'item_subtotal' => ['100.000'],
            'alamat_pengiriman' => 'Jl. Merdeka 1',
            'provinsi' => 'Jawa Tengah',
            'kota_kabupaten' => 'Semarang',
            'kecamatan' => 'Candisari',
            'kelurahan' => 'Gisikdrono',
            'kode_pos' => '50244',
            'catatan' => null,
        ]);

        $controller = new PesananController();
        $response = $controller->update($request, $pesananId);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(100000.0, (float) DB::table('detail_pesanan')->where('pesanan_id', $pesananId)->value('harga_satuan'));
    }
}
