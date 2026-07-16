<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{


    use WithoutModelEvents;



    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        $this->call([
            BankCustomerSeeder::class,
        ]);

        // Create Super Admin
        $superAdmin = User::query()->updateOrCreate([
            'email' => 'owner@sumux.id',
        ], [
            'name' => 'Owner SUMUX',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create Regular Admin
        $admin = User::query()->updateOrCreate([
            'email' => 'admin@sumux.id',
        ], [
            'name' => 'Admin SUMUX',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $kategori = [
            ['nama_kategori' => 'Interior Custom', 'slug' => 'interior-custom', 'deskripsi' => 'Produk interior custom untuk rumah dan kantor.'],
            ['nama_kategori' => 'Furniture', 'slug' => 'furniture', 'deskripsi' => 'Meja, kursi, kabinet, dan furniture siap pakai.'],
            ['nama_kategori' => 'Jasa Renovasi', 'slug' => 'jasa-renovasi', 'deskripsi' => 'Layanan renovasi dan pemasangan interior.'],
        ];

        foreach ($kategori as $index => $item) {
            DB::table('kategori_produk')->updateOrInsert([
                'slug' => $item['slug'],
            ], [
                'nama_kategori' => $item['nama_kategori'],
                'deskripsi' => $item['deskripsi'],
                'aktif' => true,
                'urutan' => $index + 1,
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);
        }

        $kategoriIds = DB::table('kategori_produk')->pluck('id', 'slug');

        $produk = [
            ['kode_produk' => 'PRD-KITCHEN-001', 'nama_produk' => 'Kitchen Set Minimalis', 'slug' => 'kitchen-set-minimalis', 'kategori' => 'interior-custom', 'jenis' => 'interior', 'harga' => 25000000, 'berat' => 120000],
            ['kode_produk' => 'PRD-WARDROBE-001', 'nama_produk' => 'Wardrobe Custom', 'slug' => 'wardrobe-custom', 'kategori' => 'interior-custom', 'jenis' => 'interior', 'harga' => 18500000, 'berat' => 95000],
            ['kode_produk' => 'PRD-MEJA-001', 'nama_produk' => 'Meja Kerja Premium', 'slug' => 'meja-kerja-premium', 'kategori' => 'furniture', 'jenis' => 'furniture', 'harga' => 4500000, 'berat' => 35000],
            ['kode_produk' => 'SRV-RENOV-001', 'nama_produk' => 'Jasa Renovasi Ruangan', 'slug' => 'jasa-renovasi-ruangan', 'kategori' => 'jasa-renovasi', 'jenis' => 'jasa', 'harga' => 12000000, 'berat' => 0],
        ];

        foreach ($produk as $item) {
            DB::table('produk')->updateOrInsert([
                'kode_produk' => $item['kode_produk'],
            ], [
                'kategori_produk_id' => $kategoriIds[$item['kategori']],
                'nama_produk' => $item['nama_produk'],
                'slug' => $item['slug'],
                'jenis_produk' => $item['jenis'],
                'deskripsi' => 'Data dummy untuk halaman pesanan.',
                'harga' => $item['harga'],
                'stok' => 12,
                'berat_gram' => $item['berat'],
                'satuan' => 'pcs',
                'aktif' => true,
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);
        }

        $produkIds = DB::table('produk')->pluck('id', 'kode_produk');

        $kurir = [
            ['kode_kurir' => 'jne', 'nama_kurir' => 'JNE', 'nama_layanan' => 'REG', 'urutan' => 1],
            ['kode_kurir' => 'jnt', 'nama_kurir' => 'J&T', 'nama_layanan' => 'EZ', 'urutan' => 2],
            ['kode_kurir' => 'cargo-internal', 'nama_kurir' => 'Cargo Internal', 'nama_layanan' => 'Truck', 'urutan' => 3],
        ];

        foreach ($kurir as $item) {
            DB::table('kurir')->updateOrInsert([
                'kode_kurir' => $item['kode_kurir'],
            ], [
                'nama_kurir' => $item['nama_kurir'],
                'nama_layanan' => $item['nama_layanan'],
                'aktif' => true,
                'urutan' => $item['urutan'],
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);
        }

        $kurirIds = DB::table('kurir')->pluck('id', 'kode_kurir');

        $pelanggan = [
            ['kode' => 'CUST-001', 'nama' => 'Budi Santoso', 'email' => 'budi@example.com', 'wa' => '081234567001', 'alamat' => 'Rembang, Jawa Tengah'],
            ['kode' => 'CUST-002', 'nama' => 'Siti Aminah', 'email' => 'siti@example.com', 'wa' => '081234567002', 'alamat' => 'Pati, Jawa Tengah'],
            ['kode' => 'CUST-003', 'nama' => 'Andi Pratama', 'email' => 'andi@example.com', 'wa' => '081234567003', 'alamat' => 'Semarang, Jawa Tengah'],
            ['kode' => 'CUST-004', 'nama' => 'Maya Lestari', 'email' => 'maya@example.com', 'wa' => '081234567004', 'alamat' => 'Kudus, Jawa Tengah'],
            ['kode' => 'CUST-005', 'nama' => 'Rizky Hidayat', 'email' => 'rizky@example.com', 'wa' => '081234567005', 'alamat' => 'Jepara, Jawa Tengah'],
            ['kode' => 'CUST-006', 'nama' => 'Nina Kartika', 'email' => 'nina@example.com', 'wa' => '081234567006', 'alamat' => 'Surabaya, Jawa Timur'],
            ['kode' => 'CUST-007', 'nama' => 'Fajar Nugroho', 'email' => 'fajar@example.com', 'wa' => '081234567007', 'alamat' => 'Yogyakarta'],
            ['kode' => 'CUST-008', 'nama' => 'Dewi Maharani', 'email' => 'dewi@example.com', 'wa' => '081234567008', 'alamat' => 'Solo, Jawa Tengah'],
        ];

        foreach ($pelanggan as $item) {
            DB::table('pelanggan')->updateOrInsert([
                'kode_pelanggan' => $item['kode'],
            ], [
                'nama_pelanggan' => $item['nama'],
                'email' => $item['email'],
                'nomor_whatsapp' => $item['wa'],
                'alamat' => $item['alamat'],
                'provinsi' => str_contains($item['alamat'], 'Jawa Timur') ? 'Jawa Timur' : 'Jawa Tengah',
                'kota' => explode(',', $item['alamat'])[0],
                'catatan' => 'Data dummy pelanggan.',
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);
        }

        $pelangganIds = DB::table('pelanggan')->pluck('id', 'kode_pelanggan');

        $orders = [
            ['nomor' => 'ORD-202606-001', 'pelanggan' => 'CUST-001', 'tanggal' => '2026-06-18', 'status' => 'produksi', 'packing' => 'belum', 'tagihan' => 25000000, 'dibayar' => 20000000, 'bank' => 'BCA', 'kurir' => 'jne', 'items' => [['PRD-KITCHEN-001', 'Kitchen Set Minimalis', 1, 25000000]]],
            ['nomor' => 'ORD-202606-002', 'pelanggan' => 'CUST-002', 'tanggal' => '2026-06-19', 'status' => 'finishing', 'packing' => 'proses', 'tagihan' => 23000000, 'dibayar' => 12000000, 'bank' => 'Mandiri', 'kurir' => 'cargo-internal', 'items' => [['PRD-WARDROBE-001', 'Wardrobe Custom', 1, 18500000], ['PRD-MEJA-001', 'Meja Kerja Premium', 1, 4500000]]],
            ['nomor' => 'ORD-202606-003', 'pelanggan' => 'CUST-003', 'tanggal' => '2026-06-20', 'status' => 'pending', 'packing' => 'belum', 'tagihan' => 12000000, 'dibayar' => 0, 'bank' => null, 'kurir' => 'jnt', 'items' => [['SRV-RENOV-001', 'Jasa Renovasi Ruangan', 1, 12000000]]],
            ['nomor' => 'ORD-202606-004', 'pelanggan' => 'CUST-004', 'tanggal' => '2026-06-21', 'status' => 'selesai', 'packing' => 'selesai', 'tagihan' => 29500000, 'dibayar' => 29500000, 'bank' => 'BRI', 'kurir' => 'cargo-internal', 'items' => [['PRD-KITCHEN-001', 'Kitchen Set Minimalis', 1, 25000000], ['PRD-MEJA-001', 'Meja Kerja Premium', 1, 4500000]]],
            ['nomor' => 'ORD-202606-005', 'pelanggan' => 'CUST-005', 'tanggal' => '2026-06-22', 'status' => 'dikirim', 'packing' => 'selesai', 'tagihan' => 18500000, 'dibayar' => 18500000, 'bank' => 'BNI', 'kurir' => 'jne', 'items' => [['PRD-WARDROBE-001', 'Wardrobe Custom', 1, 18500000]]],
            ['nomor' => 'ORD-202606-006', 'pelanggan' => 'CUST-006', 'tanggal' => '2026-06-23', 'status' => 'produksi', 'packing' => 'belum', 'tagihan' => 16500000, 'dibayar' => 8000000, 'bank' => 'BCA', 'kurir' => 'jnt', 'items' => [['SRV-RENOV-001', 'Jasa Renovasi Ruangan', 1, 12000000], ['PRD-MEJA-001', 'Meja Kerja Premium', 1, 4500000]]],
            ['nomor' => 'ORD-202606-007', 'pelanggan' => 'CUST-007', 'tanggal' => '2026-06-24', 'status' => 'finishing', 'packing' => 'proses', 'tagihan' => 25000000, 'dibayar' => 15000000, 'bank' => 'Mandiri', 'kurir' => 'cargo-internal', 'items' => [['PRD-KITCHEN-001', 'Kitchen Set Minimalis', 1, 25000000]]],
            ['nomor' => 'ORD-202606-008', 'pelanggan' => 'CUST-008', 'tanggal' => '2026-06-25', 'status' => 'selesai', 'packing' => 'selesai', 'tagihan' => 4500000, 'dibayar' => 4500000, 'bank' => 'BCA', 'kurir' => 'jne', 'items' => [['PRD-MEJA-001', 'Meja Kerja Premium', 1, 4500000]]],
        ];

        $orderNumbers = collect($orders)->pluck('nomor');
        $orderIds = DB::table('pesanan')->whereIn('nomor_pesanan', $orderNumbers)->pluck('id');
        DB::table('detail_pesanan')->whereIn('pesanan_id', $orderIds)->delete();
        DB::table('pembayaran')->whereIn('pesanan_id', $orderIds)->delete();
        DB::table('pengiriman')->whereIn('pesanan_id', $orderIds)->delete();
        DB::table('riwayat_status_pesanan')->whereIn('pesanan_id', $orderIds)->delete();

        foreach ($orders as $order) {
            $statusPembayaran = $order['dibayar'] <= 0 ? 'belum_bayar' : ($order['dibayar'] < $order['tagihan'] ? 'DP' : 'lunas');

            DB::table('pesanan')->updateOrInsert([
                'nomor_pesanan' => $order['nomor'],
            ], [
                'pelanggan_id' => $pelangganIds[$order['pelanggan']],
                'pengguna_id' => $admin->id,
                'tanggal_pesanan' => $order['tanggal'],
                'status_pesanan' => $order['status'],
                'status_pembayaran' => $statusPembayaran,
                'status_packing' => $order['packing'],
                'subtotal' => $order['tagihan'],
                'diskon' => 0,
                'biaya_lain' => 0,
                'biaya_ongkir' => $order['kurir'] === 'cargo-internal' ? 500000 : 75000,
                'total_tagihan' => $order['tagihan'],
                'total_dibayar' => $order['dibayar'],
                'sisa_tagihan' => $order['tagihan'] - $order['dibayar'],
                'alamat_pengiriman' => DB::table('pelanggan')->where('id', $pelangganIds[$order['pelanggan']])->value('alamat'),
                'provinsi_pengiriman' => 'Jawa Tengah',
                'catatan' => 'Pesanan dummy untuk halaman admin.',
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);

            $pesananId = DB::table('pesanan')->where('nomor_pesanan', $order['nomor'])->value('id');

            foreach ($order['items'] as $item) {
                DB::table('detail_pesanan')->insert([
                    'pesanan_id' => $pesananId,
                    'produk_id' => $produkIds[$item[0]],
                    'nama_item' => $item[1],
                    'jumlah' => $item[2],
                    'satuan' => 'pcs',
                    'harga_satuan' => $item[3],
                    'subtotal' => $item[2] * $item[3],
                    'berat_gram' => 25000,
                    'path_preview' => 'https://placehold.co/180x180?text=' . urlencode($item[1]),
                    'catatan_produksi' => 'Data dummy detail pesanan.',
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now,
                ]);
            }

            if ($order['dibayar'] > 0) {
                DB::table('pembayaran')->insert([
                    'pesanan_id' => $pesananId,
                    'pengguna_id' => $admin->id,
                    'nomor_pembayaran' => str_replace('ORD', 'PAY', $order['nomor']),
                    'tanggal_pembayaran' => $order['tanggal'],
                    'jumlah_bayar' => $order['dibayar'],
                    'metode_pembayaran' => 'transfer',
                    'nama_bank' => $order['bank'],
                    'nama_pengirim' => DB::table('pelanggan')->where('id', $pelangganIds[$order['pelanggan']])->value('nama_pelanggan'),
                    'path_bukti_bayar' => '/storage/dummy/bukti-bayar.jpg',
                    'status_verifikasi' => 'diterima',
                    'catatan' => 'Pembayaran dummy.',
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now,
                ]);
            }

            DB::table('pengiriman')->insert([
                'pesanan_id' => $pesananId,
                'kurir_id' => $kurirIds[$order['kurir']],
                'nomor_resi' => in_array($order['status'], ['dikirim', 'selesai'], true) ? 'SMX' . str_replace('-', '', $order['nomor']) : null,
                'kode_kurir' => $order['kurir'],
                'nama_kurir' => DB::table('kurir')->where('id', $kurirIds[$order['kurir']])->value('nama_kurir'),
                'layanan_kurir' => DB::table('kurir')->where('id', $kurirIds[$order['kurir']])->value('nama_layanan'),
                'biaya_ongkir' => $order['kurir'] === 'cargo-internal' ? 500000 : 75000,
                'berat_gram' => 25000,
                'estimasi_sampai' => '2-4 hari',
                'tanggal_kirim' => $order['status'] === 'dikirim' ? $order['tanggal'] : null,
                'status_pengiriman' => $order['status'] === 'dikirim' ? 'dikirim' : 'menunggu',
                'alamat_asal' => 'Workshop SUMUX, Rembang',
                'alamat_tujuan' => DB::table('pelanggan')->where('id', $pelangganIds[$order['pelanggan']])->value('alamat'),
                'catatan' => 'Pengiriman dummy.',
                'dibuat_pada' => $now,
                'diperbarui_pada' => $now,
            ]);

            DB::table('riwayat_status_pesanan')->insert([
                'pesanan_id' => $pesananId,
                'pengguna_id' => $admin->id,
                'status_sebelum' => null,
                'status_sesudah' => $order['status'],
                'keterangan' => 'Status awal dari seeder dummy.',
                'dibuat_pada' => $now,
            ]);
        }
    }
}
