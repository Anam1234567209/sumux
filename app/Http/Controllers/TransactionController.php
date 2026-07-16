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
}
