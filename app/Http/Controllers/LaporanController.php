<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pengiriman')
            ->join('pesanan', 'pesanan.id', '=', 'pengiriman.pesanan_id')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->select([
                'pengiriman.id',
                'pengiriman.pesanan_id',
                'pengiriman.kurir_id',
                'pengiriman.nama_kurir',
                'pengiriman.layanan_kurir',
                'pengiriman.biaya_ongkir',
                'pengiriman.status_pengiriman',
                'pesanan.tanggal_pesanan',
                'pesanan.alamat_pengiriman',
                'pelanggan.nama_pelanggan',
                'pesanan.nomor_pesanan',
            ]);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($sub) use ($keyword) {
                $sub->where('pelanggan.nama_pelanggan', 'like', "%{$keyword}%")
                    ->orWhere('pesanan.alamat_pengiriman', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '>=', $request->date('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '<=', $request->date('tanggal_selesai'));
        }

        if ($request->filled('kurir_id')) {
            $query->where('pengiriman.kurir_id', $request->input('kurir_id'));
        }

        // Hitung statistik dari filter yang sama, SEBELUM di-paginate
        $totalTransaksi = (clone $query)->count();
        $totalOngkir = (clone $query)->sum('pengiriman.biaya_ongkir');
        $rataRataOngkir = $totalTransaksi > 0 ? $totalOngkir / $totalTransaksi : 0;
        $kurirAktif = DB::table('kurir')->where('aktif', true)->count();

        $shipments = $query
            ->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pengiriman.id')
            ->paginate(10)
            ->withQueryString();

        $detailPesanan = DB::table('detail_pesanan')
            ->whereIn('pesanan_id', $shipments->pluck('pesanan_id'))
            ->select('pesanan_id', 'nama_item', 'jumlah')
            ->get()
            ->groupBy('pesanan_id');

        $kurirList = DB::table('kurir')
            ->orderBy('urutan')
            ->orderBy('nama_kurir')
            ->get();

        return view('admin.laporan', [
            'shipments' => $shipments,
            'detailPesanan' => $detailPesanan,
            'kurirList' => $kurirList,
            'totalTransaksi' => $totalTransaksi,
            'totalOngkir' => $totalOngkir,
            'rataRataOngkir' => $rataRataOngkir,
            'kurirAktif' => $kurirAktif,
        ]);
    }

    public function export(Request $request)
    {
        $query = DB::table('pengiriman')
            ->join('pesanan', 'pesanan.id', '=', 'pengiriman.pesanan_id')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->select([
                'pengiriman.pesanan_id',
                'pesanan.nomor_pesanan',
                'pesanan.tanggal_pesanan',
                'pelanggan.nama_pelanggan',
                'pelanggan.email as pelanggan_email',
                'pesanan.alamat_pengiriman',
                'pengiriman.nama_kurir',
                'pengiriman.layanan_kurir',
                'pengiriman.biaya_ongkir',
                'pengiriman.status_pengiriman',
            ]);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($sub) use ($keyword) {
                $sub->where('pelanggan.nama_pelanggan', 'like', "%{$keyword}%")
                    ->orWhere('pesanan.alamat_pengiriman', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '>=', $request->date('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('pesanan.tanggal_pesanan', '<=', $request->date('tanggal_selesai'));
        }

        if ($request->filled('kurir_id')) {
            $query->where('pengiriman.kurir_id', $request->input('kurir_id'));
        }

        $rows = $query->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pengiriman.id')
            ->get();

        $details = DB::table('detail_pesanan')
            ->whereIn('pesanan_id', $rows->pluck('pesanan_id')->unique())
            ->select('pesanan_id', 'nama_item', 'jumlah')
            ->get()
            ->groupBy('pesanan_id');

        $columns = [
            'No. Pesanan',
            'Tanggal Pesanan',
            'Nama Customer',
            'Email',
            'Alamat Pengiriman',
            'Kurir',
            'Layanan Kurir',
            'Biaya Ongkir',
            'Status Pengiriman',
            'Rincian Produk',
        ];

        $filename = 'laporan_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $details, $columns) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "sep=;\r\n");
            fputcsv($handle, $columns, ';');

            foreach ($rows as $row) {
                $productSummary = $details->get($row->pesanan_id, collect())
                    ->map(fn($item) => "{$item->jumlah}x {$item->nama_item}")
                    ->take(5)
                    ->implode('; ');

                $extraItems = $details->get($row->pesanan_id, collect())->count() - 5;
                if ($extraItems > 0) {
                    $productSummary .= ' +' . $extraItems . ' lainnya';
                }

                fputcsv($handle, [
                    $row->nomor_pesanan,
                    $row->tanggal_pesanan,
                    $row->nama_pelanggan,
                    $row->pelanggan_email,
                    $row->alamat_pengiriman,
                    $row->nama_kurir,
                    $row->layanan_kurir,
                    number_format((float) $row->biaya_ongkir, 0, ',', '.'),
                    $row->status_pengiriman,
                    $productSummary,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
