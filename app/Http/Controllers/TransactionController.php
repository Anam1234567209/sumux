<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pesanan')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->select([
                'pesanan.id',
                'pesanan.nomor_pesanan',
                'pesanan.tanggal_pesanan',
                'pesanan.status_pesanan',
                'pesanan.total_tagihan',
                'pelanggan.nama_pelanggan',
                'pelanggan.email as pelanggan_email',
            ]);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('pelanggan.nama_pelanggan', 'like', "%{$keyword}%")
                    ->orWhere('pesanan.nomor_pesanan', 'like', "%{$keyword}%");
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

        $perPageOptions = ['8', '25', '50', 'all'];
        $perPage = $request->input('per_page', '8');
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = '8';
        }

        $totalCount = (clone $query)->count();
        $paginateBy = $perPage === 'all' ? max($totalCount, 1) : (int) $perPage;

        $transactions = $query
            ->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pesanan.id')
            ->paginate($paginateBy)
            ->withQueryString();

        $statusPesanan = ['produksi', 'finishing', 'selesai', 'dikirim', 'dibatalkan', 'pending'];

        return view('admin.transactions', compact('transactions', 'statusPesanan', 'perPage'));
    }

    public function export(Request $request)
    {
        $query = DB::table('pesanan')
            ->join('pelanggan', 'pelanggan.id', '=', 'pesanan.pelanggan_id')
            ->select([
                'pesanan.nomor_pesanan',
                'pesanan.tanggal_pesanan',
                'pelanggan.nama_pelanggan',
                'pelanggan.email as pelanggan_email',
                'pelanggan.nomor_whatsapp',
                'pesanan.total_tagihan',
                'pesanan.status_pesanan',
            ]);

        if ($request->filled('q')) {
            $keyword = $request->string('q')->toString();
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('pelanggan.nama_pelanggan', 'like', "%{$keyword}%")
                    ->orWhere('pesanan.nomor_pesanan', 'like', "%{$keyword}%");
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

        $rows = $query->orderByDesc('pesanan.tanggal_pesanan')
            ->orderByDesc('pesanan.id')
            ->get();

        $columns = [
            'No. Pesanan',
            'Tanggal Pesanan',
            'Nama Customer',
            'Email',
            'No. WhatsApp',
            'Total Tagihan',
            'Status Pesanan',
        ];

        $filename = 'transactions_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "sep=;\r\n");
            fputcsv($handle, $columns, ';');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->nomor_pesanan,
                    $row->tanggal_pesanan,
                    $row->nama_pelanggan,
                    $row->pelanggan_email,
                    $row->nomor_whatsapp,
                    number_format((float) $row->total_tagihan, 0, ',', '.'),
                    $row->status_pesanan,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
