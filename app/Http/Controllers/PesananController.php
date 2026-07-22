<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    private function generateNomorPesanan(?string $tanggalPesanan = null): string
    {
        // Determine date to base the nomor on (use provided tanggal_pesanan or today)
        $dateForQuery = $tanggalPesanan ? \Carbon\Carbon::parse($tanggalPesanan)->toDateString() : today()->toDateString();
        $tanggal = $tanggalPesanan ? \Carbon\Carbon::parse($tanggalPesanan)->format('Ymd') : now()->format('Ymd');

        // Find last order for that date
        $lastOrder = DB::table('pesanan')
            ->whereDate('tanggal_pesanan', $dateForQuery)
            ->orderByDesc('id')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->nomor_pesanan, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        $base = 'SUM-' . $tanggal . '-';
        $candidate = $base . $nextNumber;

        // Ensure uniqueness by incrementing until an unused nomor_pesanan is found
        while (DB::table('pesanan')->where('nomor_pesanan', $candidate)->exists()) {
            $num = (int) substr($candidate, -4);
            $num++;
            $nextNumber = str_pad($num, 4, '0', STR_PAD_LEFT);
            $candidate = $base . $nextNumber;
        }

        return $candidate;
    }

    private function syncPengirimanStatus(int $pesananId, string $statusPesanan): void
    {
        $map = [
            'pending' => 'menunggu',
            'produksi' => 'menunggu',
            'finishing' => 'menunggu',
            'selesai' => 'diproses',
            'dikirim' => 'dikirim',
            'dibatalkan' => 'gagal',
        ];

        $statusPengiriman = $map[$statusPesanan] ?? null;
        if (! $statusPengiriman) {
            return;
        }

        DB::table('pengiriman')
            ->where('pesanan_id', $pesananId)
            ->update([
                'status_pengiriman' => $statusPengiriman,
                'diperbarui_pada' => now(),
            ]);
    }

    private function parseMoney($value): float
    {
        return (float) preg_replace('/\D/', '', (string) $value);
    }

    private function determinePaymentStatus(float $totalTagihan, float $totalDibayar): string
    {
        $sisaTagihan = max($totalTagihan - $totalDibayar, 0);

        if ($sisaTagihan <= 0) {
            return 'lunas';
        }

        if ($totalTagihan <= 0 || $sisaTagihan >= $totalTagihan) {
            return 'belum_bayar';
        }

        return 'DP';
    }

    private function getProgressStatuses(): array
    {
        $defaults = ['pending', 'produksi', 'finishing', 'selesai', 'dikirim', 'dibatalkan'];
        $custom = DB::table('progres')
            ->where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->pluck('nama')
            ->map(function (string $name): string {
                return strtolower(str_replace([' ', '/', '-'], '_', $name));
            })
            ->filter()
            ->values()
            ->all();

        return array_values(array_unique(array_merge($defaults, $custom)));
    }

    private function getPackingStatuses(): array
    {
        $custom = DB::table('packing')
            ->where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->pluck('nama')
            ->map(function (string $name): string {
                return strtolower(str_replace([' ', '/', '-'], '_', $name));
            })
            ->filter()
            ->values()
            ->all();

        return $custom ?: ['belum', 'proses', 'selesai'];
    }


    private function getEkspedisiOptions(): \Illuminate\Support\Collection
    {
        return DB::table('ekspedisi')
            ->where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();
    }

    private function getPengirimanStatus(string $statusPesanan): string
    {
        return match ($statusPesanan) {
            'pending' => 'menunggu',
            'produksi' => 'menunggu',
            'finishing' => 'menunggu',
            'selesai' => 'diproses',
            'dikirim' => 'dikirim',
            'dibatalkan' => 'gagal',
            default => 'menunggu',
        };
    }

    private function paymentMethodFromBank(?object $bank): string
    {
        return match ($bank?->kode_bank) {
            'CASH' => 'tunai',
            'QRIS' => 'qris',
            default => 'transfer',
        };
    }

    /**
     * Query dasar daftar pesanan (join + kolom + filter) dipakai bareng
     * oleh index() (tampilan tabel) dan export() (ekspor Excel/CSV).
     */
    private function buildPesananQuery(Request $request)
    {
        $query = DB::table('pesanan')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->leftJoin('pembayaran', function ($join) {
                $join->on('pembayaran.pesanan_id', '=', 'pesanan.id')
                    ->whereRaw('pembayaran.id = (
            select max(p2.id)
            from pembayaran as p2
            where p2.pesanan_id = pesanan.id
        )');
            })
            ->leftJoin('bank_customer', 'bank_customer.nama_bank', '=', 'pembayaran.nama_bank')
            ->leftJoin('pengiriman', function ($join) {
                $join->on('pengiriman.pesanan_id', '=', 'pesanan.id')
                    ->whereRaw('pengiriman.id = (
                    select max(pg2.id)
                    from pengiriman as pg2
                    where pg2.pesanan_id = pesanan.id
                )');
            })
            ->select([
                'pesanan.id',
                'pesanan.nomor_pesanan',
                'pesanan.tanggal_pesanan',
                'pesanan.status_pesanan',
                'pesanan.status_pembayaran',
                'pesanan.status_packing',
                'pesanan.total_tagihan',
                'pesanan.total_dibayar',
                'pesanan.sisa_tagihan',
                'pesanan.alamat_pengiriman',
                'pesanan.provinsi_pengiriman',
                'pesanan.kota_pengiriman',
                'pesanan.kecamatan_pengiriman',
                'pesanan.kelurahan_pengiriman',
                'pesanan.kode_pos_pengiriman',
                'pesanan.biaya_ongkir',
                'pelanggan.nama_pelanggan',
                'pelanggan.email as pelanggan_email',
                'pesanan.email as pesanan_email',
                'pelanggan.nomor_whatsapp',
                'pembayaran.metode_pembayaran',
                'pembayaran.nama_bank',
                'bank_customer.id as pembayaran_bank_id',
                'pembayaran.path_bukti_bayar',
                'pengiriman.kurir_id',
                'pengiriman.nama_kurir',
                'pengiriman.layanan_kurir',
                'pengiriman.nomor_resi',
            ]);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('pelanggan.nama_pelanggan', 'like', "%{$keyword}%")
                    ->orWhere('pesanan.nomor_pesanan', 'like', "%{$keyword}%")
                    ->orWhere('pelanggan.nomor_whatsapp', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '>=', $request->date('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '<=', $request->date('tanggal_selesai'));
        }

        if ($request->filled('status')) {
            $query->where('pesanan.status_pesanan', $request->string('status')->toString());
        }

        if ($request->has('ids')) {
            $ids = array_filter((array) $request->input('ids', []));
            if (! empty($ids)) {
                $query->whereIn('pesanan.id', $ids);
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildPesananQuery($request);

        $perPageOptions = ['8', '25', '50', 'all'];
        $perPage = $request->input('per_page', '8');
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = '8';
        }

        $totalCount = (clone $query)->count();
        $paginateBy = $perPage === 'all' ? max($totalCount, 1) : (int) $perPage;

        $pesanan = $query
            ->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pesanan.id')
            ->paginate($paginateBy)
            ->withQueryString();

        $detailPesanan = DB::table('detail_pesanan')
            ->leftJoin('produk', 'produk.id', '=', 'detail_pesanan.produk_id')
            ->whereIn('detail_pesanan.pesanan_id', $pesanan->pluck('id'))
            ->select([
                'detail_pesanan.pesanan_id',
                'detail_pesanan.nama_item',
                'detail_pesanan.jumlah',
                'detail_pesanan.satuan',
                'detail_pesanan.subtotal',
                'detail_pesanan.path_preview',
                'produk.nama_produk',
            ])
            ->orderBy('detail_pesanan.id')
            ->get()
            ->groupBy('pesanan_id');

        $metodePembayaran = DB::table('bank_customer')
            ->where('aktif', true)
            ->orderBy('nama_bank')
            ->get();

        $statusPacking = $this->getPackingStatuses();
        $statusPesanan = $this->getProgressStatuses();
        $kurir = $this->getEkspedisiOptions();

        $paymentBanks = DB::table('pembayaran')
            ->whereIn('pesanan_id', $pesanan->pluck('id'))
            ->select('pesanan_id', 'nama_bank')
            ->get()
            ->groupBy('pesanan_id');

        $paymentProofs = DB::table('pembayaran')
            ->whereIn('pesanan_id', $pesanan->pluck('id'))
            ->whereNotNull('path_bukti_bayar')
            ->select('pesanan_id', 'nama_bank', 'path_bukti_bayar', 'jumlah_bayar')
            ->orderBy('id')
            ->get()
            ->groupBy('pesanan_id');

        $progres = DB::table('progres')
            ->where('aktif', true)
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();




        return view('admin.pesanan', [
            'pesanan' => $pesanan,
            'detailPesanan' => $detailPesanan,
            'nextNomorPesanan' => $this->generateNomorPesanan(),
            'statusPesanan' => $statusPesanan,
            'statusPembayaran' => ['belum_bayar', 'DP', 'lunas'],
            'statusPacking' => $statusPacking,
            'statusPengiriman' => ['menunggu', 'diproses', 'dikirim', 'diterima', 'gagal'],
            'customers' => DB::table('pelanggan')->orderBy('nama_pelanggan')->get(),
            'products' => DB::table('produk')->where('aktif', true)->orderBy('nama_produk')->get(),
            'metodePembayaran' => $metodePembayaran,
            'kurir' => $kurir,
            'paymentBanks' => $paymentBanks,
            'paymentProofs' => $paymentProofs,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Ekspor daftar pesanan (mengikuti filter yang sedang aktif di halaman)
     * ke file CSV yang bisa langsung dibuka/dicetak lewat Excel.
     */
    public function export(Request $request)
    {
        $rows = $this->buildPesananQuery($request)
            ->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pesanan.id')
            ->get();

        $columns = [
            'No. Pesanan',
            'Tanggal Pesanan',
            'Nama Customer',
            'No. WhatsApp',
            'Email',
            'Alamat Pengiriman',
            'Status Pesanan',
            'Status Packing',
            'Total Tagihan',
            'Total Dibayar',
            'Sisa Tagihan',
            'Status Pembayaran',
            'Metode / Bank Pembayaran',
            'Biaya Ongkir',
            'Ekspedisi',
            'No. Resi',
        ];

        $filename = 'pesanan_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 supaya Excel menampilkan karakter (Rp, huruf non-latin) dengan benar
            fwrite($handle, "sep=;\r\n");
            fputcsv($handle, $columns, ';');

            foreach ($rows as $row) {
                $alamatLengkap = implode(', ', array_filter([
                    $row->alamat_pengiriman,
                    $row->kelurahan_pengiriman,
                    $row->kecamatan_pengiriman,
                    $row->kota_pengiriman,
                    $row->provinsi_pengiriman,
                    $row->kode_pos_pengiriman,
                ]));

                $ekspedisi = trim(implode(' - ', array_filter([
                    $row->nama_kurir,
                    $row->layanan_kurir,
                ])));

                fputcsv($handle, [
                    $row->nomor_pesanan,
                    $row->tanggal_pesanan,
                    $row->nama_pelanggan,
                    $row->nomor_whatsapp,
                    $row->pelanggan_email ?: $row->pesanan_email,
                    $alamatLengkap,
                    ucfirst((string) $row->status_pesanan),
                    ucfirst((string) $row->status_packing),
                    number_format((float) $row->total_tagihan, 0, ',', '.'),
                    number_format((float) $row->total_dibayar, 0, ',', '.'),
                    number_format((float) $row->sisa_tagihan, 0, ',', '.'),
                    $row->status_pembayaran,
                    trim(($row->metode_pembayaran ?? '') . ' ' . ($row->nama_bank ? "({$row->nama_bank})" : '')),
                    number_format((float) $row->biaya_ongkir, 0, ',', '.'),
                    $ekspedisi,
                    $row->nomor_resi,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function store(Request $request)
    {
        $itemPrices = array_map(fn($value) => $this->parseMoney($value), $request->input('item_price', []));
        $itemSubtotals = array_map(fn($value) => $this->parseMoney($value), $request->input('item_subtotal', []));
        $totalTagihan = $this->parseMoney($request->input('total_tagihan'));
        $totalDibayar = $this->parseMoney($request->input('total_dibayar'));
        $biayaOngkir = (int) preg_replace('/\D/', '', $request->input('biaya_ongkir', 0));
        $itemPhotos = $request->file('item_photo', []);


        if ($totalTagihan <= 0) {
            $totalTagihan = array_sum($itemSubtotals) + $biayaOngkir;
        }

        $request->merge([
            'item_price' => $itemPrices,
            'item_subtotal' => $itemSubtotals,
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
        ]);

        // Normalize empty email to null so DB nullable columns receive NULL instead of empty string
        $customerEmail = $request->filled('customer_email') ? $request->customer_email : null;
        $request->merge(['customer_email' => $customerEmail]);

        $statusPesanan = $this->getProgressStatuses();
        $statusPacking = $this->getPackingStatuses();

        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:30'],
            // 'nomor_pesanan' => ['required', 'string', 'max:100', 'unique:pesanan,nomor_pesanan'],
            'tanggal_pesanan' => ['required', 'date'],
            'status_pesanan' => ['required', Rule::in($statusPesanan)],
            'status_packing' => ['required', Rule::in($statusPacking)],
            'total_tagihan' => ['nullable', 'numeric'],
            'total_dibayar' => ['nullable', 'numeric'],
            'metode_pembayaran' => ['required', 'exists:bank_customer,id'],
            'kurir_id' => ['nullable', 'exists:kurir,id'],
            'item_name' => ['required', 'array'],
            'item_name.*' => ['nullable', 'string', 'max:255'],
            'item_qty' => ['required', 'array'],
            'item_qty.*' => ['nullable', 'numeric'],
            'item_unit' => ['required', 'array'],
            'item_unit.*' => ['nullable', 'string', 'max:50'],
            'item_price' => ['required', 'array'],
            'item_price.*' => ['nullable', 'numeric'],
            'item_subtotal' => ['required', 'array'],
            'item_subtotal.*' => ['nullable', 'numeric'],
            'item_photo' => ['nullable', 'array'],
            'item_photo.*' => ['nullable', 'image', 'max:5120'],
            'alamat_pengiriman' => ['required', 'string'],
            'provinsi' => ['required', 'string', 'max:255'],
            'kota_kabupaten' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'kelurahan' => ['required', 'string', 'max:255'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'catatan' => ['nullable', 'string'],
        ]);

        $customer = DB::table('pelanggan')->where('nama_pelanggan', $request->customer_name)->first();

        if (! $customer) {
            $customerId = DB::table('pelanggan')->insertGetId([
                'kode_pelanggan' => 'CUST-' . strtoupper(uniqid()),
                'nama_pelanggan' => $request->customer_name,
                'email' => $customerEmail,
                'nomor_whatsapp' => $request->nomor_whatsapp,
                'alamat' => $request->alamat_pengiriman,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        } else {
            $customerId = $customer->id;
        }

        $pesananId = DB::table('pesanan')->insertGetId([
            'pelanggan_id' => $customerId,
            'pengguna_id' => Auth::id(),
            'nomor_pesanan' => $this->generateNomorPesanan($request->tanggal_pesanan),
            'nomor_whatsapp' => $request->nomor_whatsapp,
            'email' => $customerEmail,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'status_pesanan' => $request->status_pesanan,
            'status_pembayaran' => $this->determinePaymentStatus($totalTagihan, $totalDibayar),
            'status_packing' => $request->status_packing,
            'subtotal' => $totalTagihan,
            'total_tagihan' => $totalTagihan,
            'biaya_ongkir' => $biayaOngkir,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => max($totalTagihan - $totalDibayar, 0),
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'provinsi_pengiriman' => $request->provinsi,
            'kota_pengiriman' => $request->kota_kabupaten,
            'kecamatan_pengiriman' => $request->kecamatan,
            'kelurahan_pengiriman' => $request->kelurahan,
            'kode_pos_pengiriman' => $request->kode_pos,
            'catatan' => $request->catatan,
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        $detailPreviewPath = null;
        if ($request->hasFile('foto_produk')) {
            $detailPreviewPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/previews', $request->file('foto_produk'));
        }

        if ($request->filled('item_name')) {
            foreach ($request->item_name as $index => $name) {
                if (blank($name)) {
                    continue;
                }

                $previewPath = $request->input("item_existing_photo.{$index}");
                if (isset($itemPhotos[$index]) && $itemPhotos[$index]->isValid()) {
                    $previewPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/previews', $itemPhotos[$index]);
                }

                if ($previewPath === null && $index === 0) {
                    $previewPath = $detailPreviewPath;
                }

                DB::table('detail_pesanan')->insert([
                    'pesanan_id' => $pesananId,
                    'nama_item' => $name,
                    'jumlah' => (int) ($request->item_qty[$index] ?? 1),
                    'satuan' => $request->item_unit[$index] ?? 'pcs',
                    'subtotal' => (float) ($itemSubtotals[$index] ?? 0),
                    'harga_satuan' => (float) ($itemPrices[$index] ?? 0),
                    'path_preview' => $previewPath,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]);
            }
        }

        $bank = DB::table('bank_customer')->find($request->metode_pembayaran);

        if ($bank) {
            $pembayaranId = DB::table('pembayaran')->insertGetId([
                'pesanan_id' => $pesananId,
                'pengguna_id' => Auth::id(),
                'nomor_pembayaran' => 'PAY-' . now()->format('YmdHis') . '-' . $pesananId,
                'tanggal_pembayaran' => now()->toDateString(),
                'jumlah_bayar' => $totalDibayar,
                'metode_pembayaran' => $this->paymentMethodFromBank($bank),
                'nama_bank' => $bank->nama_bank,
                'status_verifikasi' => 'menunggu',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);

            if ($request->hasFile('bukti_transaksi')) {
                $buktiPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/bukti', $request->file('bukti_transaksi'));
                DB::table('pembayaran')
                    ->where('id', $pembayaranId)
                    ->update(['path_bukti_bayar' => $buktiPath]);
            }
        }

        $ekspedisi = DB::table('ekspedisi')->find($request->kurir_id);

        if ($ekspedisi) {
            DB::table('pengiriman')->insert([
                'pesanan_id' => $pesananId,
                'kurir_id' => $ekspedisi->id,
                'kode_kurir' => $ekspedisi->kode,
                'nama_kurir' => $ekspedisi->nama,
                'layanan_kurir' => $ekspedisi->nama,
                'biaya_ongkir' => $biayaOngkir,
                'status_pengiriman' => $this->getPengirimanStatus($request->status_pesanan),
                'alamat_tujuan' => $request->alamat_pengiriman,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
            $this->syncPengirimanStatus($pesananId, $request->status_pesanan);
        }

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $itemSubtotals = array_map(fn($value) => $this->parseMoney($value), $request->input('item_subtotal', []));
        $totalTagihan = $this->parseMoney($request->input('total_tagihan'));
        $totalDibayar = $this->parseMoney($request->input('total_dibayar'));
        $biayaOngkir = (int) preg_replace('/\D/', '', $request->input('biaya_ongkir', 0));

        if ($totalTagihan <= 0) {
            $totalTagihan = array_sum($itemSubtotals) + $biayaOngkir;
        }

        $request->merge([
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
        ]);

        // Normalize empty email to null
        $customerEmail = $request->filled('customer_email') ? $request->customer_email : null;
        $request->merge(['customer_email' => $customerEmail]);

        $statusPesanan = $this->getProgressStatuses();
        $statusPacking = $this->getPackingStatuses();

        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:30'],
            'nomor_pesanan' => ['required', 'string', 'max:100', 'unique:pesanan,nomor_pesanan,' . $id],
            'tanggal_pesanan' => ['required', 'date'],
            'status_pesanan' => ['required', Rule::in($statusPesanan)],
            'status_packing' => ['required', Rule::in($statusPacking)],
            'total_tagihan' => ['nullable', 'numeric'],
            'total_dibayar' => ['nullable', 'numeric'],
            'kurir_id' => ['nullable', 'exists:kurir,id'],
            'item_name' => ['required', 'array'],
            'item_name.*' => ['nullable', 'string', 'max:255'],
            'item_qty' => ['required', 'array'],
            'item_qty.*' => ['nullable', 'numeric'],
            'item_unit' => ['required', 'array'],
            'item_unit.*' => ['nullable', 'string', 'max:50'],
            'item_price' => ['required', 'array'],
            'item_price.*' => ['nullable', 'numeric'],
            'item_subtotal' => ['required', 'array'],
            'item_subtotal.*' => ['nullable', 'numeric'],
            'item_photo' => ['nullable', 'array'],
            'item_photo.*' => ['nullable', 'image', 'max:5120'],
            'item_existing_photo' => ['nullable', 'array'],
            'item_existing_photo.*' => ['nullable', 'string'],
            'alamat_pengiriman' => ['nullable', 'string'],
            'provinsi' => ['required', 'string', 'max:255'],
            'kota_kabupaten' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'kelurahan' => ['required', 'string', 'max:255'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'catatan' => ['nullable', 'string'],
        ]);

        $order = DB::table('pesanan')->find($id);
        if (! $order) {
            abort(404);
        }

        $customer = DB::table('pelanggan')->where('nama_pelanggan', $request->customer_name)->first();
        if (! $customer) {
            $customerId = DB::table('pelanggan')->insertGetId([
                'kode_pelanggan' => 'CUST-' . strtoupper(uniqid()),
                'nama_pelanggan' => $request->customer_name,
                'email' => $customerEmail,
                'nomor_whatsapp' => $request->nomor_whatsapp,
                'alamat' => $request->alamat_pengiriman,
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
        } else {
            $customerId = $customer->id;
        }

        DB::table('pesanan')->where('id', $id)->update([
            'pelanggan_id' => $customerId,
            'nomor_pesanan' => $request->nomor_pesanan,
            'nomor_whatsapp' => $request->nomor_whatsapp,
            'email' => $customerEmail,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'status_pesanan' => $request->status_pesanan,
            'status_pembayaran' => $this->determinePaymentStatus($totalTagihan, $totalDibayar),
            'status_packing' => $request->status_packing,
            'subtotal' => $totalTagihan,
            'total_tagihan' => $totalTagihan,
            'biaya_ongkir' => $biayaOngkir,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => max($totalTagihan - $totalDibayar, 0),
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'provinsi_pengiriman' => $request->provinsi,
            'kota_pengiriman' => $request->kota_kabupaten,
            'kecamatan_pengiriman' => $request->kecamatan,
            'kelurahan_pengiriman' => $request->kelurahan,
            'kode_pos_pengiriman' => $request->kode_pos,
            'catatan' => $request->catatan,
            'diperbarui_pada' => now(),
        ]);

        $itemPhotos = $request->file('item_photo', []);
        $existingPhotos = $request->input('item_existing_photo', []);
        $detailPreviewPath = null;

        if ($request->hasFile('foto_produk')) {
            $detailPreviewPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/previews', $request->file('foto_produk'));
        }

        DB::table('detail_pesanan')->where('pesanan_id', $id)->delete();
        if ($request->filled('item_name')) {
            foreach ($request->item_name as $index => $name) {
                if (blank($name)) {
                    continue;
                }

                $previewPath = $existingPhotos[$index] ?? null;
                if (isset($itemPhotos[$index]) && $itemPhotos[$index]->isValid()) {
                    $previewPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/previews', $itemPhotos[$index]);
                }

                if ($previewPath === null && $index === 0) {
                    $previewPath = $detailPreviewPath;
                }

                DB::table('detail_pesanan')->insert([
                    'pesanan_id' => $id,
                    'nama_item' => $name,
                    'jumlah' => (int) ($request->item_qty[$index] ?? 1),
                    'satuan' => $request->item_unit[$index] ?? 'pcs',
                    'subtotal' => (float) ($itemSubtotals[$index] ?? 0),
                    'harga_satuan' => (float) ($request->input('item_price')[$index] ?? 0),
                    'path_preview' => $previewPath,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]);
            }
        }

        if ($request->hasFile('bukti_transaksi')) {
            $buktiPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/bukti', $request->file('bukti_transaksi'));
            $latestPaymentId = DB::table('pembayaran')
                ->where('pesanan_id', $id)
                ->orderByDesc('id')
                ->value('id');

            if ($latestPaymentId) {
                DB::table('pembayaran')
                    ->where('id', $latestPaymentId)
                    ->update(['path_bukti_bayar' => $buktiPath]);
            }
        }

        $ekspedisi = $request->filled('kurir_id') ? DB::table('ekspedisi')->find($request->kurir_id) : null;

        if ($ekspedisi) {
            $existingPengiriman = DB::table('pengiriman')
                ->where('pesanan_id', $id)
                ->orderByDesc('id')
                ->first();

            $pengirimanData = [
                'kurir_id' => $ekspedisi->id,
                'kode_kurir' => $ekspedisi->kode,
                'nama_kurir' => $ekspedisi->nama,
                'layanan_kurir' => $ekspedisi->nama,
                'biaya_ongkir' => $biayaOngkir,
                'alamat_tujuan' => $request->alamat_pengiriman,
                'diperbarui_pada' => now(),
            ];

            if ($existingPengiriman) {
                DB::table('pengiriman')->where('id', $existingPengiriman->id)->update($pengirimanData);
            } else {
                DB::table('pengiriman')->insert(array_merge($pengirimanData, [
                    'pesanan_id' => $id,
                    'status_pengiriman' => 'menunggu',
                    'dibuat_pada' => now(),
                ]));
            }
        }

        $this->syncPengirimanStatus($id, $request->status_pesanan);
        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil diperbarui');
    }

    public function edit($id)
    {
        $order = DB::table('pesanan')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->leftJoin('pembayaran', function ($join) {
                $join->on('pembayaran.pesanan_id', '=', 'pesanan.id')
                    ->whereRaw('pembayaran.id = (
                    select max(p2.id)
                    from pembayaran as p2
                    where p2.pesanan_id = pesanan.id
                )');
            })
            ->leftJoin('bank_customer', 'bank_customer.nama_bank', '=', 'pembayaran.nama_bank')
            ->leftJoin('pengiriman', function ($join) {
                $join->on('pengiriman.pesanan_id', '=', 'pesanan.id')
                    ->whereRaw('pengiriman.id = (
                    select max(pg2.id)
                    from pengiriman as pg2
                    where pg2.pesanan_id = pesanan.id
                )');
            })
            ->where('pesanan.id', $id)
            ->select([
                'pesanan.id',
                'pesanan.nomor_pesanan',
                'pesanan.tanggal_pesanan',
                'pesanan.status_pesanan',
                'pesanan.status_packing',
                'pesanan.total_tagihan',
                'pesanan.total_dibayar',
                'pesanan.sisa_tagihan',
                'pesanan.biaya_ongkir',
                'pesanan.alamat_pengiriman',
                'pelanggan.nama_pelanggan',
                'pelanggan.email as pelanggan_email',
                'pelanggan.nomor_whatsapp',
                'bank_customer.id as pembayaran_bank_id',
                'pembayaran.metode_pembayaran',
                'pembayaran.nama_bank',
                'pengiriman.kurir_id',
                'pengiriman.nama_kurir',
                'pengiriman.layanan_kurir',
            ])
            ->first();

        if (! $order) {
            abort(404);
        }

        $details = DB::table('detail_pesanan')
            ->where('pesanan_id', $id)
            ->get();

        return response()->json([
            'id' => $order->id,
            'nomor_pesanan' => $order->nomor_pesanan,
            'customer_name' => $order->nama_pelanggan,
            'nomor_whatsapp' => $order->nomor_whatsapp,
            'customer_email' => $order->pelanggan_email,
            'alamat_pengiriman' => $order->alamat_pengiriman,
            'provinsi' => $order->provinsi_pengiriman,
            'kota_kabupaten' => $order->kota_pengiriman,
            'kecamatan' => $order->kecamatan_pengiriman,
            'kelurahan' => $order->kelurahan_pengiriman,
            'kode_pos' => $order->kode_pos_pengiriman,
            'tanggal_pesanan' => $order->tanggal_pesanan,
            'status_pesanan' => $order->status_pesanan,
            'status_packing' => $order->status_packing,
            'total_tagihan' => (float) $order->total_tagihan,
            'total_dibayar' => (float) $order->total_dibayar,
            'sisa_tagihan' => (float) $order->sisa_tagihan,
            'biaya_ongkir' => (float) ($order->biaya_ongkir),
            'metode_pembayaran_id' => $order->pembayaran_bank_id,
            'metode_pembayaran' => $order->metode_pembayaran,
            'nama_bank' => $order->nama_bank,
            'kurir_id' => $order->kurir_id,
            'items' => $details->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->nama_item,
                    'price' => (float) $item->harga_satuan,
                    'qty' => $item->jumlah,
                    'unit' => $item->satuan,
                    'subtotal' => (float) $item->subtotal,
                    'photo_url' => $item->path_preview,
                ];
            }),
        ]);
    }

    public function quickUpdate(Request $request, $id)
    {
        $order = DB::table('pesanan')->where('id', $id)->first();
        if (! $order) {
            abort(404);
        }

        $statusPesanan = $this->getProgressStatuses();
        $statusPacking = $this->getPackingStatuses();

        $request->validate([
            'field' => ['required', 'in:status_pesanan,status_packing,kurir_id'],
            'value' => ['nullable', 'string'],
        ]);

        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'status_pesanan') {
            $request->validate([
                'value' => ['required', Rule::in($statusPesanan)],
            ]);
            DB::table('pesanan')->where('id', $id)->update([
                'status_pesanan' => $value,
                'diperbarui_pada' => now(),
            ]);
            $this->syncPengirimanStatus($id, $value);
        }

        if ($field === 'status_packing') {
            $request->validate([
                'value' => ['required', Rule::in($statusPacking)],
            ]);
            DB::table('pesanan')->where('id', $id)->update([
                'status_packing' => $value,
                'diperbarui_pada' => now(),
            ]);
        }

        if ($field === 'kurir_id') {
            $request->validate([
                'value' => ['required', 'exists:ekspedisi,id'],
            ]);
            $ekspedisi = DB::table('ekspedisi')->find($value);

            $existingPengiriman = DB::table('pengiriman')
                ->where('pesanan_id', $id)
                ->orderByDesc('id')
                ->first();

            $pengirimanData = [
                'kurir_id' => $ekspedisi->id,
                'kode_kurir' => $ekspedisi->kode,
                'nama_kurir' => $ekspedisi->nama,
                'layanan_kurir' => $ekspedisi->nama,
                'alamat_tujuan' => $order->alamat_pengiriman,
                'diperbarui_pada' => now(),
            ];

            if ($existingPengiriman) {
                DB::table('pengiriman')->where('id', $existingPengiriman->id)->update($pengirimanData);
            } else {
                DB::table('pengiriman')->insert(array_merge($pengirimanData, [
                    'pesanan_id' => $id,
                    'status_pengiriman' => 'menunggu',
                    'dibuat_pada' => now(),
                ]));
            }
        }

        return response()->json(['success' => true]);
    }

    public function addPayment(Request $request, $id)
    {
        $order = DB::table('pesanan')->where('id', $id)->first();
        if (! $order) {
            abort(404);
        }

        $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'bank_id' => ['required', 'exists:bank_customer,id'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:5120'],
        ]);

        $jumlahBayar = $this->parseMoney($request->input('jumlah_bayar'));
        $bank = DB::table('bank_customer')->find($request->input('bank_id'));

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = '/storage/' . Storage::disk('public')->put('uploads/pesanan/bukti', $request->file('bukti_pembayaran'));
        }

        $paymentCount = DB::table('pembayaran')->where('pesanan_id', $id)->count();

        DB::table('pembayaran')->insert([
            'pesanan_id' => $id,
            'pengguna_id' => Auth::id(),
            'nomor_pembayaran' => 'PAY-' . now()->format('YmdHis') . '-' . $id . '-' . ($paymentCount + 1),
            'tanggal_pembayaran' => now()->toDateString(),
            'jumlah_bayar' => $jumlahBayar,
            'metode_pembayaran' => $this->paymentMethodFromBank($bank),
            'nama_bank' => $bank->nama_bank,
            'path_bukti_bayar' => $buktiPath,
            'status_verifikasi' => 'diterima',
            'dibuat_pada' => now(),
            'diperbarui_pada' => now(),
        ]);

        $totalDibayar = (float) DB::table('pembayaran')
            ->where('pesanan_id', $id)
            ->where('status_verifikasi', '!=', 'ditolak')
            ->sum('jumlah_bayar');

        $totalTagihan = (float) $order->total_tagihan;
        $sisaTagihan = max($totalTagihan - $totalDibayar, 0);
        $statusPembayaran = $this->determinePaymentStatus($totalTagihan, $totalDibayar);

        DB::table('pesanan')->where('id', $id)->update([
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan,
            'status_pembayaran' => $statusPembayaran,
            'diperbarui_pada' => now(),
        ]);



        return response()->json(['success' => true]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids) || empty($ids)) {
            return redirect()->route('admin.pesanan')->with('success', 'Tidak ada pesanan yang dipilih');
        }

        $validIds = array_filter($ids, fn($v) => is_numeric($v));

        DB::table('detail_pesanan')->whereIn('pesanan_id', $validIds)->delete();
        DB::table('pembayaran')->whereIn('pesanan_id', $validIds)->delete();
        DB::table('pengiriman')->whereIn('pesanan_id', $validIds)->delete();
        DB::table('pesanan')->whereIn('id', $validIds)->delete();

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan terpilih berhasil dihapus');
    }

    public function destroy($id)
    {
        $order = DB::table('pesanan')->find($id);
        if (! $order) {
            abort(404);
        }

        DB::table('detail_pesanan')->where('pesanan_id', $id)->delete();
        DB::table('pembayaran')->where('pesanan_id', $id)->delete();
        DB::table('pengiriman')->where('pesanan_id', $id)->delete();
        DB::table('pesanan')->where('id', $id)->delete();

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dihapus');
    }
}
